<?php

namespace App\Filament\User\Resources\EventRegistrations\Tables;

use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EventRegistrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
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
                TextColumn::make("total_amount")->money("IDR")->sortable(),
                TextColumn::make("updated_at")->since(),
            ])
            ->defaultSort("created_at", "desc")
            ->recordActions([
                ViewAction::make(),
                EditAction::make()->visible(
                    fn($record): bool => !(
                        $record->status === RegistrationStatus::Paid ||
                        $record->payment_status === PaymentStatus::Verified
                    ),
                ),
            ]);
    }
}
