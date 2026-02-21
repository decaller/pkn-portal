<?php

namespace App\Filament\User\Resources\Events\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_image')
                    ->circular()
                    ->label(''),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('event_date')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('description')
                    ->limit(80)
                    ->label('Summary'),
            ])
            ->defaultSort('event_date', 'desc')
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
