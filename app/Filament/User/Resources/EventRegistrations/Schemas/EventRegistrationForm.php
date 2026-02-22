<?php

namespace App\Filament\User\Resources\EventRegistrations\Schemas;

use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Models\Event;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class EventRegistrationForm
{
    // User-facing registration form (tenant-scoped) with auto pricing logic.
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make("Registration")
                ->columnSpanFull()
                ->schema([
                    Select::make("event_id")
                        ->relationship("event", "title")
                        // Allow preselecting an event from a dashboard link.
                        ->default(fn() => request()->query("event_id"))
                        ->live()
                        ->afterStateUpdated(function (
                            Get $get,
                            Set $set,
                            $livewire,
                        ): void {
                            // When the event changes we refresh package rows.
                            self::refreshPackageRowsForEvent(
                                $get,
                                $set,
                                $livewire,
                            );
                        })
                        ->afterStateHydrated(function (
                            ?int $state,
                            Get $get,
                            Set $set,
                            $livewire,
                        ): void {
                            // No debug logging in production form.
                        })
                        ->required()
                        ->searchable()
                        ->preload(),
                    Repeater::make("package_breakdown")
                        ->label("Package participants")
                        ->columnSpanFull()
                        ->schema([
                            Select::make("package_name")
                                ->label("Package")
                                ->options(
                                    fn(Get $get): array => self::packageOptions(
                                        $get("../../event_id"),
                                    ),
                                )
                                ->searchable()
                                ->preload()
                                ->live()
                                ->afterStateUpdated(function (
                                    Get $get,
                                    Set $set,
                                    ?string $state,
                                    $livewire,
                                ): void {
                                    // Compute and set this row's unit_price = price * qty.
                                    $qty = max(
                                        1,
                                        (int) ($get("participant_count") ?? 1),
                                    );
                                    $price = self::packagePrice(
                                        $get("../../event_id"),
                                        $state,
                                    );
                                    $set("unit_price", $price * $qty);
                                    // Let the repeater-level callback perform the authoritative sync.
                                })
                                ->required(),
                            TextInput::make("participant_count")
                                ->label("Participants")
                                ->numeric()
                                ->minValue(1)
                                ->default(1)
                                ->live()
                                ->afterStateUpdated(function (
                                    Get $get,
                                    Set $set,
                                    $livewire,
                                ): void {
                                    // Update the row's unit_price when participant count changes.
                                    $package = $get("package_name");
                                    $price = self::packagePrice(
                                        $get("../../event_id"),
                                        $package,
                                    );
                                    $qty = max(
                                        1,
                                        (int) ($get("participant_count") ?? 1),
                                    );
                                    $set("unit_price", $price * $qty);
                                    // Let the repeater-level callback perform the authoritative sync.
                                })
                                ->required(),
                            TextInput::make("unit_price")
                                ->label("Unit price")
                                ->numeric()
                                ->prefix("IDR")
                                ->default(0)
                                // Price is derived from the package, not directly editable.
                                ->disabled()
                                // Disabled fields don't dehydrate unless explicitly set.
                                ->dehydrated()
                                ->required(),
                        ])
                        ->columns(3)
                        ->addActionLabel("Add package row")
                        // Keep totals in sync when rows are added/removed.
                        ->live()
                        ->afterStateUpdated(function (
                            ?array $state,
                            Set $set,
                            $livewire,
                        ): void {
                            // Recompute totals using the up-to-date repeater state.
                            self::syncTotalAmount($state ?? [], $set);
                        })
                        ->afterStateHydrated(function (
                            ?array $state,
                            Get $get,
                            Set $set,
                            $livewire,
                        ): void {
                            $eventId = $get("event_id");

                            if (!$eventId) {
                                return;
                            }

                            $rows = $state ?? [];
                            $firstPackage = $rows[0]["package_name"] ?? null;

                            if ($firstPackage) {
                                return;
                            }

                            self::seedFirstPackageRow($get, $set, $livewire);
                        }),
                    // The package details are persisted in `package_breakdown` (JSON array).
                    // No mirrored top-level `package_name`, `participant_count`, or `unit_price`
                    // fields are necessary for the user-facing form.
                    Hidden::make("booker_user_id")->default(
                        fn() => auth()->id(),
                    ),
                    // Organization is always the current tenant.
                    Hidden::make("organization_id")->default(
                        fn() => filament()->getTenant()?->getKey(),
                    ),
                    TextInput::make("total_amount")
                        ->numeric()
                        ->prefix("IDR")
                        // Total is computed from package rows.
                        ->readOnly()
                        ->required(),
                    // User-facing registrations always start as draft + unpaid.
                    Hidden::make("status")->default(
                        RegistrationStatus::Draft->value,
                    ),
                    Hidden::make("payment_status")->default(
                        PaymentStatus::Unpaid->value,
                    ),
                    FileUpload::make("payment_proof_path")
                        ->label("Payment proof")
                        ->disk("public")
                        ->visibility("public")
                        ->directory("payment-proofs")
                        ->maxSize(4096),
                    // Removed debug_log field from the user-facing form.
                ])
                ->columns(1),
        ]);
    }

    private static function packageOptions(?int $eventId): array
    {
        $packages = self::packagesForEvent($eventId);

        if ($packages === []) {
            return ["General" => "General (IDR 0)"];
        }

        $options = [];

        foreach ($packages as $package) {
            $name = (string) ($package["name"] ?? "General");
            $price = (float) ($package["price"] ?? 0);

            $options[$name] = sprintf(
                "%s (IDR %s)",
                $name,
                number_format($price, 0, ",", "."),
            );
        }

        return $options;
    }

    private static function packagePrice(
        ?int $eventId,
        ?string $packageName,
    ): float {
        $packages = self::packagesForEvent($eventId);

        if ($packages === []) {
            return 0;
        }

        foreach ($packages as $package) {
            if ((string) ($package["name"] ?? "") !== (string) $packageName) {
                continue;
            }

            return (float) ($package["price"] ?? 0);
        }

        return 0;
    }

    private static function packagesForEvent(?int $eventId): array
    {
        if (!$eventId) {
            return [];
        }

        /** @var Event|null $event */
        $event = Event::query()->find($eventId);

        if (!$event) {
            return [];
        }

        $packages = $event->registration_packages;

        return is_array($packages) ? $packages : [];
    }

    private static function refreshPackageRowsForEvent(
        Get $get,
        Set $set,
        $livewire,
    ): void {
        $eventId = $get("event_id");
        $rows = $get("package_breakdown") ?? [];

        if ($rows === []) {
            return;
        }

        $refreshed = [];

        foreach ($rows as $row) {
            $qty = max(1, (int) ($row["participant_count"] ?? 1));
            $refreshed[] = [
                "package_name" => null,
                "participant_count" => $qty,
                "unit_price" => 0,
            ];
        }

        $set("package_breakdown", $refreshed);
        self::syncTotalAmount($refreshed, $set);
    }

    private static function seedFirstPackageRow(
        Get $get,
        Set $set,
        $livewire,
    ): void {
        $eventId = $get("event_id");
        $options = self::packageOptions($eventId);
        $firstPackage = array_key_first($options);

        if (!$firstPackage) {
            return;
        }

        $rows = [
            [
                "package_name" => $firstPackage,
                "participant_count" => 1,
                "unit_price" => self::packagePrice($eventId, $firstPackage),
            ],
        ];

        $set("package_breakdown", $rows);
        self::syncTotalAmount($rows, $set);
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     */
    private static function syncTotalAmount(array $rows, Set $set): void
    {
        $total = 0.0;

        foreach ($rows as $row) {
            $total += (float) ($row["unit_price"] ?? 0);
        }

        $set("total_amount", $total);
    }
}
