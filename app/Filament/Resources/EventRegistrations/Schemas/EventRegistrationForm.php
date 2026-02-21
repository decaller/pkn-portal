<?php

namespace App\Filament\Resources\EventRegistrations\Schemas;

use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Models\Event;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class EventRegistrationForm
{
    // THIS IS FOR ADMIN
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make("Registration")
                ->columnSpanFull()
                ->schema([
                    Select::make("event_id")
                        ->relationship("event", "title")
                        ->default(fn() => request()->query("event_id"))
                        ->live()
                        ->afterStateUpdated(function (Set $set): void {
                            $set("package_breakdown", []);
                            $set("package_name", null);
                            $set("participant_count", 0);
                            $set("unit_price", 0);
                            $set("total_amount", 0);
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
                                ): void {
                                    if (!$get("use_custom_price")) {
                                        $set(
                                            "unit_price",
                                            self::packagePrice(
                                                $get("../../event_id"),
                                                $state,
                                            ),
                                        );
                                    }
                                })
                                ->required(),
                            TextInput::make("participant_count")
                                ->label("Participants")
                                ->numeric()
                                ->minValue(1)
                                ->default(1)
                                ->required(),
                            Toggle::make("use_custom_price")
                                ->label("Custom price")
                                ->visible(fn(): bool => self::isMainAdmin())
                                ->default(false)
                                ->dehydrated(false),
                            TextInput::make("unit_price")
                                ->label("Unit price")
                                ->numeric()
                                ->prefix("IDR")
                                ->default(0)
                                ->disabled(
                                    fn(
                                        Get $get,
                                    ): bool => !self::isMainAdmin() ||
                                        !$get("use_custom_price"),
                                )
                                ->dehydrated()
                                ->required(),
                        ])
                        ->columns(4)
                        ->defaultItems(0)
                        ->addActionLabel("Add package row")
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (
                            ?array $state,
                            Set $set,
                        ): void {
                            self::syncPricingSummary($state ?? [], $set);
                        }),

                    Hidden::make("package_name"),
                    Hidden::make("participant_count"),
                    Hidden::make("unit_price"),

                    Select::make("booker_user_id")
                        ->label("Booker")
                        ->relationship("booker", "name")
                        ->required()
                        ->searchable()
                        ->preload(),
                    Select::make("organization_id")
                        ->label("Organization")
                        ->relationship("organization", "name")
                        ->searchable()
                        ->preload()
                        ->nullable(),
                    TextInput::make("total_amount")
                        ->numeric()
                        ->prefix("IDR")
                        ->readOnly()
                        ->required(),
                    Select::make("status")
                        ->options(
                            collect(RegistrationStatus::cases())->mapWithKeys(
                                fn($case) => [
                                    $case->value => ucfirst(
                                        str_replace("_", " ", $case->value),
                                    ),
                                ],
                            ),
                        )
                        ->required(),
                    Select::make("payment_status")
                        ->options(
                            collect(PaymentStatus::cases())->mapWithKeys(
                                fn($case) => [
                                    $case->value => ucfirst($case->value),
                                ],
                            ),
                        )
                        ->required(),
                    FileUpload::make("payment_proof_path")
                        ->label("Payment proof")
                        ->disk("public")
                        ->visibility("public")
                        ->directory("payment-proofs")
                        ->maxSize(4096),
                    Select::make("verified_by_user_id")
                        ->label("Verified by")
                        ->relationship("verifier", "name")
                        ->default(fn() => auth()->id())
                        ->searchable()
                        ->preload()
                        ->nullable(),
                    DateTimePicker::make("verified_at")
                        ->seconds(false)
                        ->nullable(),
                    Textarea::make("notes")
                        ->rows(4)
                        ->maxLength(1000)
                        ->columnSpanFull(),
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

    /**
     * @param array<int, array<string, mixed>> $rows
     */
    private static function syncPricingSummary(array $rows, Set $set): void
    {
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

    private static function isMainAdmin(): bool
    {
        return (bool) auth()->user()?->isMainAdmin();
    }
}
