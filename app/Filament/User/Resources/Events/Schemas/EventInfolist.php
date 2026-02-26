<?php

namespace App\Filament\User\Resources\Events\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EventInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 1, 'md' => 2])->schema([
                    Group::make([
                        Section::make('Event Guidelines')
                            ->schema([
                                TextEntry::make('title')->weight('bold')->size('lg'),
                                TextEntry::make('description')->markdown()->prose(),
                                TextEntry::make('event_date')->date('d M Y')->icon('heroicon-m-calendar-days'),
                                TextEntry::make('capacity')
                                    ->label('Availability')
                                    ->icon('heroicon-m-user-group')
                                    ->state(function (\App\Models\Event $event) {
                                        if (is_null($event->max_capacity)) {
                                            return 'Unlimited Spots';
                                        }
                                        $available = $event->availableSpots();

                                        return $available > 0
                                            ? "{$available} spots remaining out of {$event->max_capacity}"
                                            : "Sold Out ({$event->max_capacity} capacity reached)";
                                    })
                                    ->badge()
                                    ->color(fn (\App\Models\Event $event) => $event->isFull() ? 'danger' : 'success'),
                            ]),
                    ])->columnSpan(['md' => 1]),

                    Group::make([
                        Section::make('Media')
                            ->schema([
                                ImageEntry::make('cover_image')->hiddenLabel(),
                                ImageEntry::make('photos')
                                    ->hiddenLabel()
                                    ->stacked()
                                    ->circular(),
                            ]),
                    ])->columnSpan(['md' => 1]),
                ])
                ->columnSpanFull(),

                Section::make('Sessions & Rundown')
                    ->schema([
                        RepeatableEntry::make('rundown')
                            ->hiddenLabel()
                            ->schema([
                                TextEntry::make('title')
                                    ->weight('bold')
                                    ->size('lg'),
                                TextEntry::make('speaker')
                                    ->icon('heroicon-m-user')
                                    ->badge()
                                    ->color('info'),
                                TextEntry::make('description')
                                    ->html()
                                    ->prose()
                                    ->columnSpanFull()
                                    ->visible(fn ($state) => filled($state)),
                                RepeatableEntry::make('links')
                                    ->schema([
                                        TextEntry::make('label')->label('Resource'),
                                        TextEntry::make('url')->label('URL')->url(fn ($state) => $state, true)->color('primary'),
                                    ])
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->visible(fn ($state) => filled($state)),
                            ])
                            ->columns(2),
                    ])
                    ->visible(fn ($record) => filled($record->rundown))
                    ->columnSpanFull(),
            ]);
    }
}
