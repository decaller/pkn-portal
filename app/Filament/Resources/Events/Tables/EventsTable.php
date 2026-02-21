<?php

namespace App\Filament\Resources\Events\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;

class EventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // 1. The Cover Image (Circle)
                ImageColumn::make("cover_image")->circular()->label(""),

                // 2. The Title & Slug
                TextColumn::make("title")
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => $record->slug) // Shows slug in small gray text below title
                    ->weight("bold"),

                // 3. The Date (Formatted)
                TextColumn::make("event_date")
                    ->date("d M Y") // Example: 16 Feb 2026
                    ->sortable()
                    ->label("Event Date"),

                // 4. Quick Publish Toggle
                // This lets you click to publish/unpublish directly from the list!
                ToggleColumn::make("is_published")
                    ->label("Published")
                    ->onColor("success")
                    ->offColor("danger"),

                ToggleColumn::make("allow_registration")
                    ->label("Registration Open")
                    ->onColor("success")
                    ->offColor("gray"),

                // 5. Creation Date (Hidden by default, useful for debugging)
                TextColumn::make("created_at")
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort("event_date", "desc")
            ->filters([
                //
            ])
            ->recordActions([ViewAction::make(), EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
