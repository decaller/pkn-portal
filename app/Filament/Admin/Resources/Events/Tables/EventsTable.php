<?php

namespace App\Filament\Admin\Resources\Events\Tables;

use App\Enums\EventType;
use App\Filament\Admin\Resources\Events\EventResource;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class EventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // 1. The Cover Image (Circle)
                ImageColumn::make('cover_image')->circular()->label(''),

                // 2. The Title & Slug
                TextColumn::make('title')
                    ->translateLabel()
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->slug) // Shows slug in small gray text below title
                    ->weight('bold'),

                // 3. The Date (Formatted)
                TextColumn::make('event_date')
                    ->date('d M Y') // Example: 16 Feb 2026
                    ->sortable()
                    ->placeholder('-')
                    ->label(__('Event Date')),

                TextColumn::make('event_type')
                    ->label(__('Type'))
                    ->badge()
                    ->formatStateUsing(
                        fn (
                            EventType|string|null $state,
                        ): string => $state instanceof EventType
                            ? $state->getLabel()
                            : EventType::tryFrom(
                                (string) $state,
                            )?->getLabel() ?? '-',
                    )
                    ->color(
                        fn (
                            EventType|string|null $state,
                        ): string|array|null => $state instanceof EventType
                            ? $state->getColor()
                            : EventType::tryFrom((string) $state)?->getColor(),
                    )
                    ->sortable(),

                // 4. Quick Publish Toggle
                // This lets you click to publish/unpublish directly from the list!
                ToggleColumn::make('is_published')
                    ->label(__('Published'))
                    ->onColor('success')
                    ->offColor('danger'),

                ToggleColumn::make('allow_registration')
                    ->label(__('Registration Open'))
                    ->onColor('success')
                    ->offColor('gray'),

                // 5. Creation Date (Hidden by default, useful for debugging)
                TextColumn::make('created_at')
                    ->translateLabel()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('event_date', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('activities')
                    ->label(__('Activities'))
                    ->icon('heroicon-o-clock')
                    ->url(fn ($record) => EventResource::getUrl('activities', ['record' => $record])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
