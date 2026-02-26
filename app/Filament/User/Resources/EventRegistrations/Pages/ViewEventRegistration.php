<?php

namespace App\Filament\User\Resources\EventRegistrations\Pages;

use App\Enums\PaymentStatus;
use App\Filament\User\Resources\EventRegistrations\EventRegistrationResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ViewEventRegistration extends ViewRecord
{
    protected static string $resource = EventRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make("view_event")
                ->label("View Event")
                ->icon("heroicon-o-calendar")
                ->color("info")
                ->url(fn(): string => route("filament.user.resources.events.view", [
                    "tenant" => $this->record->organization?->slug ?? "personal",
                    "record" => $this->record->event_id,
                ])),
            Action::make("edit_participants")
                ->label("Edit Participants")
                ->icon("heroicon-o-users")
                ->color("warning")
                ->visible(fn(): bool => auth()->user()->can("manageParticipants", $this->record))
                ->fillForm(fn(): array => [
                    "packages" => $this->packageEntriesForForm(),
                ])
                ->form([
                    Repeater::make("packages")
                        ->label("Packages")
                        ->schema([
                            TextInput::make("package_name")
                                ->label("Package")
                                ->disabled()
                                ->dehydrated(),
                            Repeater::make("participants")
                                ->label("Participants")
                                ->schema([
                                    Select::make("user_id")
                                        ->label("Select from organization")
                                        ->options(fn(Get $get): array => $this->organizationUserOptionsExcluding(
                                            $get("../../../../packages"),
                                            $get("user_id"),
                                        ))
                                        ->searchable()
                                        ->nullable()
                                        ->live()
                                        ->afterStateUpdated(function (
                                            ?int $state,
                                            Set $set,
                                        ): void {
                                            if (!$state) {
                                                $set("full_name", null);
                                                $set("phone", null);
                                                return;
                                            }

                                            $user = User::find($state);
                                            if ($user) {
                                                $set("full_name", $user->name);
                                                $set("phone", $user->phone_number ?? "");
                                            }
                                        }),
                                    TextInput::make("full_name")
                                        ->label("Full name")
                                        ->disabled(fn(Get $get): bool => (bool) $get("user_id"))
                                        ->dehydrated(),
                                    TextInput::make("phone")
                                        ->label("Phone number")
                                        ->disabled(fn(Get $get): bool => (bool) $get("user_id"))
                                        ->dehydrated(),
                                ])
                                ->columns(3)
                                ->columnSpanFull()
                                ->addable(false)
                                ->deletable(false)
                                ->reorderable(false),
                        ])
                        ->columnSpanFull()
                        ->addable(false)
                        ->deletable(false)
                        ->reorderable(false),
                ])
                ->action(function (array $data): void {
                    $this->ensureRegistrationEditable();

                    $packages = is_array($data["packages"] ?? null)
                        ? array_values($data["packages"])
                        : [];

                    $seenUserIds = [];
                    $seenPhones = [];

                    // Validate all entries across all packages
                    foreach ($packages as $pkgIndex => $package) {
                        $participants = is_array($package["participants"] ?? null)
                            ? array_values($package["participants"])
                            : [];

                        foreach ($participants as $pIndex => $entry) {
                            $userId = $entry["user_id"] ? (int) $entry["user_id"] : null;
                            $fullName = trim((string) ($entry["full_name"] ?? ""));
                            $phone = $this->normalizePhone($entry["phone"] ?? "");

                            if ($userId) {
                                if (isset($seenUserIds[$userId])) {
                                    throw ValidationException::withMessages([
                                        "packages.{$pkgIndex}.participants.{$pIndex}.user_id" => "This participant has already been selected in another slot.",
                                    ]);
                                }
                                $seenUserIds[$userId] = true;
                            } else {
                                // New user — name required
                                if ($fullName === "") {
                                    // Empty slot — skip (no participant for this slot)
                                    continue;
                                }

                                if ($phone === "") {
                                    throw ValidationException::withMessages([
                                        "packages.{$pkgIndex}.participants.{$pIndex}.phone" => "Phone number is required when adding a new participant.",
                                    ]);
                                }

                                if (User::query()->where("phone_number", $phone)->exists()) {
                                    throw ValidationException::withMessages([
                                        "packages.{$pkgIndex}.participants.{$pIndex}.phone" => "Phone number {$phone} is already registered. Please select the user from the organization list instead.",
                                    ]);
                                }

                                if (isset($seenPhones[$phone])) {
                                    throw ValidationException::withMessages([
                                        "packages.{$pkgIndex}.participants.{$pIndex}.phone" => "Phone number {$phone} is duplicated in this form.",
                                    ]);
                                }

                                $seenPhones[$phone] = true;
                            }
                        }
                    }

                    DB::transaction(function () use ($packages): void {
                        $this->record->participants()->delete();

                        foreach ($packages as $package) {
                            $participants = is_array($package["participants"] ?? null)
                                ? array_values($package["participants"])
                                : [];

                            foreach ($participants as $entry) {
                                $userId = $entry["user_id"] ? (int) $entry["user_id"] : null;
                                $fullName = trim((string) ($entry["full_name"] ?? ""));
                                $phone = $this->normalizePhone($entry["phone"] ?? "");

                                if ($userId) {
                                    $user = User::find($userId);
                                    if (!$user) {
                                        continue;
                                    }
                                } else {
                                    if ($fullName === "") {
                                        // Empty slot — skip
                                        continue;
                                    }

                                    $user = User::create([
                                        "name" => $fullName,
                                        "email" => Str::uuid() . "@participant.local",
                                        "phone_number" => $phone ?: null,
                                        "password" => Str::random(40),
                                    ]);

                                    if ($this->record->organization) {
                                        $this->record->organization
                                            ->users()
                                            ->syncWithoutDetaching([
                                                $user->getKey() => ["role" => "member"],
                                            ]);
                                    }
                                }

                                $this->record->participants()->create([
                                    "user_id" => $user->getKey(),
                                    "name" => $user->name,
                                    "email" => $user->email,
                                    "phone" => $user->phone_number,
                                ]);
                            }
                        }
                    });

                    Notification::make()
                        ->title("Participants updated.")
                        ->success()
                        ->send();

                    $this->redirect(
                        static::getResource()::getUrl("view", [
                            "record" => $this->record,
                        ]),
                    );
                }),
            Action::make("upload_payment_proof")
                ->label("Upload Payment")
                ->icon("heroicon-o-arrow-up-tray")
                ->color("success")
                ->visible(
                    fn(): bool =>
                        auth()->user()->can("updatePayment", $this->record) &&
                        $this->record->payment_status !== PaymentStatus::Verified,
                )
                ->form([
                    FileUpload::make("payment_proof_path")
                        ->label("Payment proof")
                        ->disk("public")
                        ->visibility("public")
                        ->directory("payment-proofs")
                        ->maxSize(4096)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $this->record->submitPaymentProof(
                        $data["payment_proof_path"],
                    );

                    $this->redirect(
                        static::getResource()::getUrl("view", [
                            "record" => $this->record,
                        ]),
                    );
                }),
        ];
    }

    /**
     * Build the form data: one outer entry per package, each with N inner
     * participant entries equal to the registered quota for that package.
     * Pre-fills existing participants into the first package's slots.
     */
    private function packageEntriesForForm(): array
    {
        $rows = is_array($this->record->package_breakdown)
            ? $this->record->package_breakdown
            : [];

        if ($rows === []) {
            $rows = [
                [
                    "package_name" => "General",
                    "participant_count" => 1,
                ],
            ];
        }

        // Load existing participants (ordered by creation)
        $existingParticipants = $this->record
            ->participants()
            ->with("user")
            ->get();

        $participantIndex = 0;
        $packages = [];

        foreach ($rows as $row) {
            $packageName = (string) ($row["package_name"] ?? "General");
            $quota = max(1, (int) ($row["participant_count"] ?? 1));

            $participantEntries = [];

            for ($i = 0; $i < $quota; $i++) {
                $participant = $existingParticipants->get($participantIndex);
                $participantIndex++;

                if ($participant && $participant->user_id) {
                    $participantEntries[] = [
                        "user_id" => $participant->user_id,
                        "full_name" => $participant->name ?? $participant->user?->name ?? "",
                        "phone" => $participant->phone ?? $participant->user?->phone_number ?? "",
                    ];
                } else {
                    $participantEntries[] = [
                        "user_id" => null,
                        "full_name" => $participant?->name ?? "",
                        "phone" => $participant?->phone ?? "",
                    ];
                }
            }

            $packages[] = [
                "package_name" => $packageName,
                "participants" => $participantEntries,
            ];
        }

        return $packages;
    }

    /**
     * Return org user options excluding users already selected in any slot
     * across all packages, except the current slot's own selection.
     *
     * @param  array|null  $allPackages  All outer repeater entries (from Get)
     * @param  mixed       $currentUserId  The user_id of the current entry
     */
    private function organizationUserOptionsExcluding(?array $allPackages, mixed $currentUserId): array
    {
        if (!$this->record->organization) {
            return [];
        }

        // Collect all user IDs already selected across all packages/slots
        $selectedIds = [];
        foreach ((array) $allPackages as $package) {
            foreach ((array) ($package["participants"] ?? []) as $entry) {
                $uid = $entry["user_id"] ?? null;
                if ($uid && (int) $uid !== (int) $currentUserId) {
                    $selectedIds[] = (int) $uid;
                }
            }
        }

        return $this->record->organization
            ->users()
            ->whereNotIn("users.id", $selectedIds)
            ->orderBy("name")
            ->pluck("name", "users.id")
            ->all();
    }

    private function ensureRegistrationEditable(): void
    {
        if ($this->record->canRemoveParticipants()) {
            return;
        }

        throw ValidationException::withMessages([
            "participants" =>
                "Participants cannot be modified after payment has been submitted.",
        ]);
    }

    private function normalizePhone(string $phone): string
    {
        return trim($phone);
    }
}
