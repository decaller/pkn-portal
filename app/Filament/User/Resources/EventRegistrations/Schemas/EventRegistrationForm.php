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
                            // When the user changes the event, reset packages to the first option.
                            self::appendDebugLog(
                                $get,
                                $set,
                                "Event updated: " .
                                    ($get("event_id") ?? "null"),
                                $livewire,
                            );
                            self::seedFirstPackageRow($get, $set);
                        })
                        ->afterStateHydrated(function (
                            ?int $state,
                            Get $get,
                            Set $set,
                            $livewire,
                        ): void {
                            // When the event is prefilled (e.g., from query), seed packages once.
                            self::appendDebugLog(
                                $get,
                                $set,
                                "Hydrating event: " .
                                    ($state ?? "null") .
                                    " (event_id=" .
                                    ($get("event_id") ?? "null") .
                                    ")",
                                $livewire,
                            );
                            $eventId = $get("event_id");

                            if (!$eventId) {
                                return;
                            }

                            // Avoid overwriting existing rows when editing a record.
                            $rows = $get("package_breakdown") ?? [];
                            $firstPackage = $rows[0]["package_name"] ?? null;

                            if ($firstPackage) {
                                self::appendDebugLog(
                                    $get,
                                    $set,
                                    "Hydrating skipped: rows already set.",
                                    $livewire,
                                );
                                return;
                            }

                            self::seedFirstPackageRow($get, $set);
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
                                    // Update unit price when package changes, then recalc totals.
                                    self::appendDebugLog(
                                        $get,
                                        $set,
                                        "Package changed to: " .
                                            ($state ?? "null"),
                                        $livewire,
                                    );
                                    $price = self::packagePrice(
                                        $get("../../event_id"),
                                        $state,
                                    );
                                    $set("unit_price", $price);
                                    self::syncPricingSummary(
                                        $get("../../package_breakdown"),
                                        $set,
                                    );
                                })
                                ->required(),
                            TextInput::make("participant_count")
                                ->label("Participants")
                                ->numeric()
                                ->minValue(1)
                                ->default(1)
                                // Recalculate when the user finishes editing the count.
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (
                                    Get $get,
                                    Set $set,
                                    $livewire,
                                ): void {
                                    // Count changes affect total amount.
                                    self::appendDebugLog(
                                        $get,
                                        $set,
                                        "Participant count updated.",
                                        $livewire,
                                    );
                                    self::syncPricingSummary(
                                        $get("../../package_breakdown"),
                                        $set,
                                    );
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
                            // Recalculate totals whenever the repeater changes.
                            self::appendDebugLog(
                                null,
                                $set,
                                "Repeater updated: " .
                                    count($state ?? []) .
                                    " row(s).",
                                $livewire,
                            );
                            self::syncPricingSummary($state ?? [], $set);
                        })
                        ->afterStateHydrated(function (
                            ?array $state,
                            Get $get,
                            Set $set,
                            $livewire,
                        ): void {
                            // If editing a record, just sync the totals.
                            if (!empty($state)) {
                                self::appendDebugLog(
                                    $get,
                                    $set,
                                    "Repeater hydrated with existing rows.",
                                    $livewire,
                                );
                                self::syncPricingSummary($state, $set);
                                return;
                            }

                            // For new records, seed with the first available package.
                            $eventId = $get("event_id");
                            $options = self::packageOptions($eventId);
                            $firstPackage = array_key_first($options);

                            if (!$firstPackage) {
                                self::appendDebugLog(
                                    $get,
                                    $set,
                                    "No packages available for event.",
                                    $livewire,
                                );
                                return;
                            }

                            $initialRows = [
                                [
                                    "package_name" => $firstPackage,
                                    "participant_count" => 1,
                                    "unit_price" => self::packagePrice(
                                        $eventId,
                                        $firstPackage,
                                    ),
                                ],
                            ];

                            $set("package_breakdown", $initialRows);
                            self::appendDebugLog(
                                $get,
                                $set,
                                "Seeded initial package row.",
                                $livewire,
                            );
                            self::syncPricingSummary($initialRows, $set);
                        }),
                    // Mirror the first row into flat columns expected by the model.
                    Hidden::make("package_name"),
                    Hidden::make("participant_count"),
                    Hidden::make("unit_price"),
                    // Booker is always the current user.
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
                        ->maxSize(4096)
                        ->afterStateUpdated(function (
                            Set $set,
                            $livewire,
                        ): void {
                            // If a file is uploaded, mark payment as submitted.
                            self::appendDebugLog(
                                null,
                                $set,
                                "Payment proof updated.",
                                $livewire,
                            );
                            $set(
                                "payment_status",
                                PaymentStatus::Submitted->value,
                            );
                        }),
                    Textarea::make("debug_log")
                        ->label("Debug log")
                        ->rows(6)
                        ->readOnly()
                        ->dehydrated(false)
                        ->columnSpanFull(),
                ])
                ->columns(1),
        ]);
    }

    private static function packageOptions(?int $eventId): array
    {
        // Build a labeled list of packages for the selected event.
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
        // Find the price for a given package name in the selected event.
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
        // Safely load packages; return empty when event is missing.
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

    /**
     * @param array<int, array<string, mixed>> $rows
     */
    private static function syncPricingSummary(array $rows, Set $set): void
    {
        // Aggregate total and mirror the first row into flat columns.
        $total = 0.0;

        foreach ($rows as $row) {
            $qty = max(1, (int) ($row["participant_count"] ?? 1));
            $unit = (float) ($row["unit_price"] ?? 0);
            $total += $qty * $unit;
        }

        $first = $rows[0] ?? [];

        $set("package_name", (string) ($first["package_name"] ?? ""));
        $set(
            "participant_count",
            max(0, (int) ($first["participant_count"] ?? 0)),
        );
        $set("unit_price", (float) ($first["unit_price"] ?? 0));
        $set("total_amount", $total);
    }

    private static function seedFirstPackageRow(Get $get, Set $set): void
    {
        // Initialize the repeater with the first available package row.
        $eventId = $get("event_id");

        self::appendDebugLog(
            $get,
            $set,
            "Seeded package row from event: " . ($eventId ?? "null"),
        );

        $options = self::packageOptions($eventId);
        $firstPackage = array_key_first($options);

        $rows = [];

        if ($firstPackage !== null) {
            $rows[] = [
                "package_name" => $firstPackage,
                "participant_count" => 1,
                "unit_price" => self::packagePrice($eventId, $firstPackage),
            ];
        }

        $set("package_breakdown", $rows);

        self::syncPricingSummary($rows, $set);
    }

    private static function appendDebugLog(
        ?Get $get,
        Set $set,
        string $message,
        $livewire = null,
    ): void {
        if ($livewire) {
            $livewire->dispatch("log-data", message: $message);
        }

        $existing = $get ? $get("debug_log") : "";
        $next = $existing === "" ? $message : $existing . "\n" . $message;
        $set("debug_log", $next);
    }
}
