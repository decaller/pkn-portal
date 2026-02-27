<?php

namespace App\Filament\Admin\Resources\Analytics\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AnalyticsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // 1. Show the User's Name, not just their ID
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                // 2. Color-coded Action (View, Download, etc.)
                TextColumn::make('action')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'view' => 'info',
                        'download' => 'success',
                        'delete' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),

                // 3. The "Item Type" (News, Event, Document)
                TextColumn::make('trackable_type')
                    ->label('Type')
                    ->formatStateUsing(fn (string $state): string => class_basename($state)) // "News", not "App\Models\News"
                    ->badge()
                    ->color('warning'),

                // 4. The "Item Name" (The Magic Part)
                TextColumn::make('trackable_id')
                    ->label('Target Item')
                    ->formatStateUsing(function ($record) {
                        // Dynamically tries to find the 'title' or 'name' of the item
                        return $record->trackable->title
                            ?? $record->trackable->name
                            ?? 'Item #'.$record->trackable_id;
                    })
                    ->limit(40),

                TextColumn::make('platform')
                    ->label('Device/Platform')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Time'),
            ])
            ->defaultSort('created_at', 'desc') // Show newest logs first
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                // EditAction::make(),
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
