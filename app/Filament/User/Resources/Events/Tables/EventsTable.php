<?php

namespace App\Filament\User\Resources\Events\Tables;

use App\Enums\EventType;
use App\Filament\User\Resources\EventRegistrations\EventRegistrationResource;
use App\Models\Event;
use Filament\Actions\Action;
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
                ImageColumn::make('cover_image')->circular()->label(''),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('event_date')->date('d M Y')->sortable(),
                TextColumn::make('event_type')
                    ->label('Type')
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
                TextColumn::make('description')->limit(80)->label('Summary'),
            ])
            ->defaultSort('event_date', 'desc')
            ->recordActions([
                ViewAction::make(),
                Action::make('register')
                    ->label('Register')
                    ->icon('heroicon-o-ticket')
                    ->color('success')
                    ->visible(fn (Event $record): bool => $record->allow_registration && $record->event_date >= now()->toDateString() && ! $record->isFull())
                    ->url(fn (Event $record): string => EventRegistrationResource::getUrl('create', [
                        'event_id' => $record->getKey(),
                    ])),
            ]);
    }
}
