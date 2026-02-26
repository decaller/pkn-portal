<?php

namespace App\Filament\User\Resources\EventRegistrations\Tables;

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
                TextColumn::make("status")->badge(),
                TextColumn::make("payment_status")->badge(),
                // TextColumn::make("total_amount")->money("IDR")->sortable(),
                // TextColumn::make("updated_at")->since(),
            ])
            ->defaultSort("created_at", "desc")
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
