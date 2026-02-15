<?php

namespace App\Filament\Resources\News\Tables;

use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;

class NewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // 1. The Thumbnail (Circular for modern look)
                ImageColumn::make('thumbnail')
                    ->circular()
                    ->label(''), // Empty label looks cleaner for icons

                // 2. The Title (Searchable & Bold)
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(50), // Truncate long titles

                // 3. The Analytics Magic (Counts the related records)
                TextColumn::make('analytics_count')
                    ->counts('analytics') // This requires the relationship in your News model
                    ->label('Views')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                // 4. Status (Icon instead of just "Yes/No" text)
                IconColumn::make('is_published')
                    ->boolean()
                    ->label('Published')
                    ->sortable(),

                // 5. Date (Hidden by default to keep table clean)
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
