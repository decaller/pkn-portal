<?php

namespace App\Filament\User\Resources\EventRegistrations\Tables;

use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Filament\User\Resources\EventRegistrations\EventRegistrationResource;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EventRegistrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->searchable(false)
            ->recordUrl(
                fn($record): string => EventRegistrationResource::getUrl("view", [
                    "record" => $record,
                ]),
            )
            ->columns([
                TextColumn::make("event.title")
                    ->label("Event")
                    ->searchable()
                    ->sortable(),
                TextColumn::make("organization.name")
                    ->label("Organization")
                    ->placeholder("Personal"),
                TextColumn::make("status")
                    ->badge()
                    ->formatStateUsing(fn(RegistrationStatus|string|null $state): string => $state instanceof RegistrationStatus ? $state->getLabel() : RegistrationStatus::tryFrom((string) $state)?->getLabel() ?? '-')
                    ->color(fn(RegistrationStatus|string|null $state): string => $state instanceof RegistrationStatus ? $state->getColor() : RegistrationStatus::tryFrom((string) $state)?->getColor() ?? 'gray'),
                TextColumn::make("payment_status")
                    ->badge()
                    ->formatStateUsing(fn(PaymentStatus|string|null $state): string => $state instanceof PaymentStatus ? $state->getLabel() : PaymentStatus::tryFrom((string) $state)?->getLabel() ?? '-')
                    ->color(fn(PaymentStatus|string|null $state): string => $state instanceof PaymentStatus ? $state->getColor() : PaymentStatus::tryFrom((string) $state)?->getColor() ?? 'gray'),
            ])
            ->defaultSort("created_at", "desc")
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
