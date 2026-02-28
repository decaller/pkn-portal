<?php

namespace App\Filament\Shared\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class DocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->original_filename),

                TextColumn::make('event.title')
                    ->label('Event')
                    ->badge()
                    ->color('info'),

                TextColumn::make('tags')
                    ->label('Keywords')
                    ->badge() // 👈 This turns the tags into those nice "pills"
                    ->separator(',') // 👈 Tell Filament how to split the string/array
                    ->searchable()
                    ->color('info'), // You can change this to 'success' or 'warning'

                TextColumn::make('mime_type')
                    ->label('Type')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('content')
                    ->label('Full Text')
                    ->searchable() // 👈 This enables searching inside the PDF content!
                    ->toggleable(isToggledHiddenByDefault: true), // 👈 Keeps the table UI clean
            ])
            ->filters([
                // Add a filter to find documents by Event
                SelectFilter::make('event')
                    ->relationship('event', 'title'),
            ])
            ->recordActions([
                // DOWNLOAD ACTION
                Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(fn ($record) => Storage::disk('public')->download($record->file_path)),
                ViewAction::make(),
                // EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
