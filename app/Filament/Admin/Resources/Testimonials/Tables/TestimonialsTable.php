<?php

namespace App\Filament\Admin\Resources\Testimonials\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TestimonialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Participant / Guest')
                    ->getStateUsing(fn ($record) => $record->user ? $record->user->name : $record->guest_name)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('event.title')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                TextColumn::make('rating')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->icon('heroicon-m-star'),
                \Filament\Tables\Columns\ToggleColumn::make('is_approved')
                    ->label('Approved'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
