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
    public static function schema(): array
    {
        return [
            Select::make("event_id")
                ->options(
                    fn(): array => self::availableEventsQuery()
                        ->pluck("title", "id")
                        ->all(),
                )
                ->default(fn() => request()->query("event_id"))
                ->live()
                ->afterStateUpdated(function (
                    Get $get,
                    Set $set,
                    $livewire,
                ): void {
                    // Log event changes for debugging.
                    self::appendDebugLog(
                        $get,
                        $set,
                        "Event updated: " . ($get("event_id") ?? "null"),
                        $livewire,
                    );
                    self::resetPackageRowsForEvent(
                        $get,
                        $set,
                        $livewire,
                        seedWhenEmpty: false,
                    );
                })
                ->afterStateHydrated(function (
                    ?int $state,
                    Get $get,
                    Set $set,
                    $livewire,
                ): void {
                    // Log event hydration for debugging.
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
                })
                ->required()
                ->searchable()
                ->preload()
                ->exists(
                    table: Event::class,
                    column: "id",
                    modifyRuleUsing: fn($rule) => $rule
                        ->where("is_published", true)
                        ->where("allow_registration", true)
                        ->where(
                            fn($query) => $query->whereDate(
                                "event_date",
                                ">=",
                                now()->toDateString(),
                            ),
                        ),
                ),
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
                            // Log package changes for debugging.
                            self::appendDebugLog(
                                $get,
                                $set,
                                "Package changed to: " . ($state ?? "null"),
                                $livewire,
                            );
                            $qty = max(
                                1,
                                (int) ($get("participant_count") ?? 1),
                            );
                            $price = self::packagePrice(
                                $get("../../event_id"),
                                $state,
                            );
                            $set("unit_price", $price * $qty);
                            self::syncTotalAmount(
                                $get("../../package_breakdown"),
                                $set,
                                $livewire,
                            );
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
                            // Log participant count changes.
                            self::appendDebugLog(
                                $get,
                                $set,
                                "Participant count updated.",
                                $livewire,
                            );
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
                            self::syncTotalAmount(
                                $get("../../package_breakdown"),
                                $set,
                                $livewire,
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
                        ->live()
                        // ->afterStateUpdated(function (
                        //     Get $get,
                        //     Set $set,
                        //     $livewire,
                        // ): void {
                        //     // Log repeater changes for debugging.
                        //     self::appendDebugLog(
                        //         null,
                        //         $set,
                        //         "Unit Price Updated ",
                        //         $livewire,
                        //     );
                        //     self::syncTotalAmount(
                        //                 $get("../../package_breakdown"),
                        //                 $set("../../total_amount"),
                        //                 $livewire,
                        //             );
                        // })
                        ->required(),
                ])
                ->columns(3)
                ->addActionLabel("Add package row")
                // Keep totals in sync when rows are added/removed.
                ->live()
                // ->afterStateUpdated(function (
                //     ?array $state,
                //     Set $set,
                //     $livewire,
                // ): void {
                //     // Log repeater changes for debugging.
                //     self::appendDebugLog(
                //         null,
                //         $set,
                //         "Repeater updated: " .
                //             count($state ?? []) .
                //             " row(s).",
                //         $livewire,
                //     );
                //     self::syncTotalAmount($state ?? [], $set,$livewire);
                // })
                ->afterStateHydrated(function (
                    ?array $state,
                    Get $get,
                    Set $set,
                    $livewire,
                ): void {
                    // Log repeater hydration for debugging.
                    self::appendDebugLog(
                        $get,
                        $set,
                        "Repeater hydrated.",
                        $livewire,
                    );

                    $eventId = $get("event_id");

                    if (!$eventId) {
                        self::appendDebugLog(
                            $get,
                            $set,
                            "Repeater hydrate skipped: no event_id.",
                            $livewire,
                        );
                        return;
                    }

                    $rows = $state ?? [];
                    $firstPackage = $rows[0]["package_name"] ?? null;

                    if ($firstPackage) {
                        self::appendDebugLog(
                            $get,
                            $set,
                            "Repeater hydrate skipped: rows already set.",
                            $livewire,
                        );
                        return;
                    }

                    self::resetPackageRowsForEvent(
                        $get,
                        $set,
                        $livewire,
                        seedWhenEmpty: true,
                    );
                }),
            // Mirror the first row into flat columns expected by the model.
            // Hidden::make("package_name"),
            // Hidden::make("participant_count"),
            // Hidden::make("unit_price"),
            // Booker is always the current user.
            Hidden::make("booker_user_id")->default(fn() => auth()->id()),
            // Organization is always the current tenant.
            Hidden::make("organization_id")->default(
                fn() => filament()->getTenant()?->getKey(),
            ),
            TextInput::make("total_amount")
                ->numeric()
                ->prefix("IDR")
                ->readOnly()
                ->required()
                ->default(0)
                ->disabled(),
            // User-facing registrations always start as draft + unpaid.
            Hidden::make("status")->default(RegistrationStatus::Draft->value),
            Hidden::make("payment_status")->default(
                PaymentStatus::Unpaid->value,
            ),
            // FileUpload::make("payment_proof_path")
            //     ->label("Payment proof")
            //     ->disk("public")
            //     ->visibility("public")
            //     ->directory("payment-proofs")
            //     ->maxSize(4096)
            //     ->afterStateUpdated(function (Set $set, $livewire): void {
            //         // Log payment proof changes.
            //         self::appendDebugLog(
            //             null,
            //             $set,
            //             "Payment proof updated.",
            //             $livewire,
            //         );
            //     }),
            // Textarea::make("debug_log")
            //     ->label("Debug log")
            //     ->rows(6)
            //     ->readOnly()
            //     ->dehydrated(false)
            //     ->columnSpanFull(),
        ];
    }
    // User-facing registration form (tenant-scoped) with auto pricing logic.
    public static function configure(Schema $schema): Schema
    {
        // HOW DO I CALL IT BACK?
        return $schema->components(
            Section::make("Registration")
                ->columnSpanFull()
                ->schema(self::schema())
                ->columns(1),
        );
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
        $event = self::availableEventsQuery()->find($eventId);

        if (!$event) {
            return [];
        }

        $packages = $event->registration_packages;

        return is_array($packages) ? $packages : [];
    }

    private static function resetPackageRowsForEvent(
        Get $get,
        Set $set,
        $livewire,
        bool $seedWhenEmpty = false,
    ): void {
        $eventId = $get("event_id");
        $rows = $get("package_breakdown") ?? [];

        if ($rows === []) {
            if (!$seedWhenEmpty) {
                self::appendDebugLog(
                    $get,
                    $set,
                    "Refresh packages skipped: no rows yet.",
                    $livewire,
                );
                return;
            }

            $rows = [
                [
                    "participant_count" => 1,
                ],
            ];

            self::appendDebugLog(
                $get,
                $set,
                "Seeded first package row.",
                $livewire,
            );
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

        self::appendDebugLog(
            $get,
            $set,
            "Refreshed packages for event (reselect required): " .
                ($eventId ?? "null"),
            $livewire,
        );

        $set("package_breakdown", $refreshed);
        self::syncTotalAmount($refreshed, $set, $livewire);
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     */
    private static function syncTotalAmount(
        array $rows,
        Set $set,
        $livewire,
    ): void {
        $total = 0.0;

        self::appendDebugLog(
            null,
            $set,
            "Syncing total amount with data : " . json_encode($rows),
            $livewire,
        );

        foreach ($rows as $row) {
            $total += (float) ($row["unit_price"] ?? 0);
        }

        $set("../../total_amount", $total);

        self::appendDebugLog(
            null,
            $set,
            "Total amount synced: " . $total,
            $livewire,
        );
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

    private static function availableEventsQuery()
    {
        return Event::query()
            ->where("is_published", true)
            ->where("allow_registration", true)
            ->whereDate("event_date", ">=", now()->toDateString())
            ->orderBy("event_date");
    }
}
