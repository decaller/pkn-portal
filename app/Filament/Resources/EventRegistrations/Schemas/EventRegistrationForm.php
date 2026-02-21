<?php

namespace App\Filament\Resources\EventRegistrations\Schemas;

use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EventRegistrationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Registration')
                    ->schema([
                        Select::make('event_id')
                            ->relationship('event', 'title')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Select::make('booker_user_id')
                            ->label('Booker')
                            ->relationship('booker', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Select::make('organization_id')
                            ->label('Organization')
                            ->relationship('organization', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        TextInput::make('total_amount')
                            ->numeric()
                            ->prefix('IDR')
                            ->required(),
                        Select::make('status')
                            ->options(
                                collect(RegistrationStatus::cases())->mapWithKeys(
                                    fn ($case) => [$case->value => ucfirst(str_replace('_', ' ', $case->value))],
                                ),
                            )
                            ->required(),
                        Select::make('payment_status')
                            ->options(
                                collect(PaymentStatus::cases())->mapWithKeys(
                                    fn ($case) => [$case->value => ucfirst($case->value)],
                                ),
                            )
                            ->required(),
                        TextInput::make('payment_proof_path')
                            ->label('Payment proof path')
                            ->maxLength(255),
                        Select::make('verified_by_user_id')
                            ->label('Verified by')
                            ->relationship('verifier', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        DateTimePicker::make('verified_at')
                            ->seconds(false)
                            ->nullable(),
                        Textarea::make('notes')
                            ->rows(4)
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
