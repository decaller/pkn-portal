<?php

namespace App\Filament\User\Resources\EventRegistrations\Schemas;

use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Models\Organization;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EventRegistrationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make("Registration")
                ->schema([
                    Select::make("event_id")
                        ->relationship(
                            "event",
                            "title",
                            modifyQueryUsing: fn($query) => $query
                                ->where("is_published", true)
                                ->where("allow_registration", true)
                                ->whereDate(
                                    "event_date",
                                    ">=",
                                    now()->toDateString(),
                                ),
                        )
                        ->default(fn() => request()->query("event_id"))
                        ->required()
                        ->searchable()
                        ->preload(),
                    Select::make("organization_id")
                        ->label("Organization (optional)")
                        ->options(
                            fn() => Organization::query()
                                ->whereHas(
                                    "users",
                                    fn($query) => $query->whereKey(
                                        auth()->id(),
                                    ),
                                )
                                ->orderBy("name")
                                ->pluck("name", "id"),
                        )
                        ->searchable()
                        ->preload()
                        ->nullable(),
                    TextInput::make("total_amount")
                        ->numeric()
                        ->prefix("IDR")
                        ->default(0)
                        ->required(),
                    Textarea::make("notes")->rows(3)->maxLength(1000),
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
                        ->disabled()
                        ->dehydrated(false),
                    Select::make("payment_status")
                        ->options(
                            collect(PaymentStatus::cases())->mapWithKeys(
                                fn($case) => [
                                    $case->value => ucfirst($case->value),
                                ],
                            ),
                        )
                        ->disabled()
                        ->dehydrated(false),
                ])
                ->columns(2),
        ]);
    }
}
