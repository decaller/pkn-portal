<?php

namespace App\Filament\Admin\Resources\EventRegistrations\Pages;

use App\Enums\RegistrationStatus;
use App\Filament\Admin\Resources\EventRegistrations\EventRegistrationResource;
use App\Filament\Admin\Resources\Users\UserResource;
use App\Filament\Public\Resources\Events\EventResource;
use App\Models\EventRegistration;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
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
            Action::make('verify_payment')
                ->label('Verify Payment')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (EventRegistration $record): bool => $record->payment_status === \App\Enums\PaymentStatus::Submitted)
                ->requiresConfirmation()
                ->modalHeading('Verify Payment')
                ->modalDescription('Are you absolutely sure you want to verify this payment? The user will be fully confirmed for this event.')
                ->modalSubmitActionLabel('Yes, mark as Verified')
                ->action(function (EventRegistration $record) {
                    $record->verifyPayment(auth()->user());

                    \Filament\Notifications\Notification::make()
                        ->title('Payment Verified')
                        ->success()
                        ->send();
                }),

            Action::make('edit_participants')
                ->label('Edit Participants')
                ->icon('heroicon-o-users')
                ->color('warning')
                ->visible(fn (EventRegistration $record): bool => auth()->user()->is_super_admin || (
                    auth()->user()->can('manageParticipants', $record) && in_array($record->status, [
                        RegistrationStatus::Draft,
                        RegistrationStatus::PendingPayment,
                        RegistrationStatus::Paid,
                    ])
                ))
                ->fillForm(fn (): array => [
                    'packages' => $this->packageEntriesForForm(),
                ])
                ->form([
                    Repeater::make('packages')
                        ->label('Packages')
                        ->schema([
                            TextInput::make('package_name')
                                ->label('Package')
                                ->disabled()
                                ->dehydrated(),
                            Repeater::make('participants')
                                ->label('Participants')
                                ->schema([
                                    Select::make('user_id')
                                        ->label('Select from organization')
                                        ->options(fn (Get $get): array => $this->organizationUserOptionsExcluding(
                                            $get('../../../../packages'),
                                            $get('user_id'),
                                        ))
                                        ->searchable()
                                        ->nullable()
                                        ->live()
                                        ->afterStateUpdated(function (
                                            ?int $state,
                                            Set $set,
                                        ): void {
                                            if (! $state) {
                                                $set('full_name', null);
                                                $set('phone', null);

                                                return;
                                            }

                                            $user = User::find($state);
                                            if ($user) {
                                                $set('full_name', $user->name);
                                                $set('phone', $user->phone_number ?? '');
                                            }
                                        }),
                                    TextInput::make('full_name')
                                        ->label('Full name')
                                        ->disabled(fn (Get $get): bool => (bool) $get('user_id'))
                                        ->dehydrated(),
                                    TextInput::make('phone')
                                        ->label('Phone number')
                                        ->disabled(fn (Get $get): bool => (bool) $get('user_id'))
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

                    $packages = is_array($data['packages'] ?? null)
                        ? array_values($data['packages'])
                        : [];

                    $seenUserIds = [];
                    $seenPhones = [];

                    // Validate all entries across all packages
                    foreach ($packages as $pkgIndex => $package) {
                        $participants = is_array($package['participants'] ?? null)
                            ? array_values($package['participants'])
                            : [];

                        foreach ($participants as $pIndex => $entry) {
                            $userId = $entry['user_id'] ? (int) $entry['user_id'] : null;
                            $fullName = trim((string) ($entry['full_name'] ?? ''));
                            $phone = $this->normalizePhone($entry['phone'] ?? '');

                            if ($userId) {
                                if (isset($seenUserIds[$userId])) {
                                    throw ValidationException::withMessages([
                                        "packages.{$pkgIndex}.participants.{$pIndex}.user_id" => 'This participant has already been selected in another slot.',
                                    ]);
                                }
                                $seenUserIds[$userId] = true;
                            } else {
                                // New user — name required
                                if ($fullName === '') {
                                    // Empty slot — skip (no participant for this slot)
                                    continue;
                                }

                                if ($phone === '') {
                                    throw ValidationException::withMessages([
                                        "packages.{$pkgIndex}.participants.{$pIndex}.phone" => 'Phone number is required when adding a new participant.',
                                    ]);
                                }

                                if (User::query()->where('phone_number', $phone)->exists()) {
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
                            $participants = is_array($package['participants'] ?? null)
                                ? array_values($package['participants'])
                                : [];

                            foreach ($participants as $entry) {
                                $userId = $entry['user_id'] ? (int) $entry['user_id'] : null;
                                $fullName = trim((string) ($entry['full_name'] ?? ''));
                                $phone = $this->normalizePhone($entry['phone'] ?? '');

                                if ($userId) {
                                    $user = User::find($userId);
                                    if (! $user) {
                                        continue;
                                    }
                                } else {
                                    if ($fullName === '') {
                                        // Empty slot — skip
                                        continue;
                                    }

                                    $user = User::create([
                                        'name' => $fullName,
                                        'email' => Str::uuid().'@participant.local',
                                        'phone_number' => $phone ?: null,
                                        'password' => Str::random(40),
                                    ]);

                                    if ($this->record->organization) {
                                        $this->record->organization
                                            ->users()
                                            ->syncWithoutDetaching([
                                                $user->getKey() => ['role' => 'member'],
                                            ]);
                                    }
                                }

                                $this->record->participants()->create([
                                    'user_id' => $user->getKey(),
                                    'name' => $user->name,
                                    'email' => $user->email,
                                    'phone' => $user->phone_number,
                                ]);
                            }
                        }
                    });

                    Notification::make()
                        ->title('Participants updated.')
                        ->success()
                        ->send();

                    $this->redirect(
                        static::getResource()::getUrl('view', [
                            'record' => $this->record,
                        ]),
                    );
                }),
            Action::make('view_event')
                ->label('View Related Event')
                ->icon('heroicon-o-link')
                ->color('info')
                ->url(fn (): string => EventResource::getUrl('view', ['record' => $this->record->event_id]))
                ->visible(fn (): bool => $this->record->event_id !== null),

            Action::make('view_booker')
                ->label('View Booker Profile')
                ->icon('heroicon-o-user')
                ->color('gray')
                ->url(fn (EventRegistration $record): string => UserResource::getUrl('view', ['record' => $record->booker_user_id])),

            EditAction::make(),
        ];
    }

    private function organizationUserOptionsExcluding(?array $allPackages, mixed $currentUserId): array
    {
        if (! $this->record->organization) {
            return [];
        }

        // Collect all user IDs already selected across all packages/slots
        $selectedIds = [];
        foreach ((array) $allPackages as $package) {
            foreach ((array) ($package['participants'] ?? []) as $entry) {
                $uid = $entry['user_id'] ?? null;
                if ($uid && (int) $uid !== (int) $currentUserId) {
                    $selectedIds[] = (int) $uid;
                }
            }
        }

        return $this->record->organization
            ->users()
            ->whereNotIn('users.id', $selectedIds)
            ->orderBy('name')
            ->pluck('name', 'users.id')
            ->all();
    }

    private function ensureRegistrationEditable(): void
    {
        if (auth()->user()->is_super_admin) {
            return;
        }

        if ($this->record->canRemoveParticipants()) {
            return;
        }

        throw ValidationException::withMessages([
            'participants' => 'Participants cannot be modified after payment has been submitted.',
        ]);
    }

    private function normalizePhone(string $phone): string
    {
        return trim($phone);
    }

    private function packageEntriesForForm(): array
    {
        $rows = is_array($this->record->package_breakdown)
            ? $this->record->package_breakdown
            : [];

        if ($rows === []) {
            $rows = [
                [
                    'package_name' => 'General',
                    'participant_count' => 1,
                ],
            ];
        }

        // Load existing participants (ordered by creation)
        $existingParticipants = $this->record
            ->participants()
            ->with('user')
            ->get();

        $participantIndex = 0;
        $packages = [];

        foreach ($rows as $row) {
            $packageName = (string) ($row['package_name'] ?? 'General');
            $quota = max(1, (int) ($row['participant_count'] ?? 1));

            $participantEntries = [];

            for ($i = 0; $i < $quota; $i++) {
                $participant = $existingParticipants->get($participantIndex);
                $participantIndex++;

                if ($participant && $participant->user_id) {
                    $participantEntries[] = [
                        'user_id' => $participant->user_id,
                        'full_name' => $participant->name ?? $participant->user?->name ?? '',
                        'phone' => $participant->phone ?? $participant->user?->phone_number ?? '',
                    ];
                } else {
                    $participantEntries[] = [
                        'user_id' => null,
                        'full_name' => $participant?->name ?? '',
                        'phone' => $participant?->phone ?? '',
                    ];
                }
            }

            $packages[] = [
                'package_name' => $packageName,
                'participants' => $participantEntries,
            ];
        }

        return $packages;
    }
}
