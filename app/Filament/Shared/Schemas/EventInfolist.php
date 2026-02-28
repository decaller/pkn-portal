<?php

namespace App\Filament\Shared\Schemas;

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
                                TextEntry::make('event_type')
                                    ->badge()
                                    ->color('primary')
                                    ->icon('heroicon-m-tag'),
                                TextEntry::make('place')
                                    ->icon('heroicon-m-map-pin')
                                    ->visible(fn ($state) => filled($state)),
                                TextEntry::make('city')->visible(fn ($state) => filled($state)),
                                TextEntry::make('province')->visible(fn ($state) => filled($state)),
                                TextEntry::make('nation')->visible(fn ($state) => filled($state)),
                                TextEntry::make('google_maps_url')
                                    ->label('Map URL')
                                    ->url(fn ($state) => $state, true)
                                    ->color('primary')
                                    ->icon('heroicon-m-map')
                                    ->visible(fn ($state) => filled($state)),
                                TextEntry::make('duration_days')
                                    ->label('Duration')
                                    ->icon('heroicon-m-clock')
                                    ->formatStateUsing(fn ($state) => "{$state} Days")
                                    ->visible(fn ($state) => filled($state)),
                                TextEntry::make('allow_registration')
                                    ->badge()
                                    ->color(fn ($state) => $state ? 'success' : 'danger')
                                    ->formatStateUsing(fn ($state) => $state ? 'Open' : 'Closed'),
                                TextEntry::make('tags')
                                    ->badge()
                                    ->separator(',')
                                    ->columnSpanFull(),
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
                        Section::make('Promotional Image')
                            ->schema([
                                ImageEntry::make('cover_image')->hiddenLabel(),
                                ImageEntry::make('photos')
                                    ->hiddenLabel()
                                    ->stacked()
                                    ->circular(),
                            ]),
                        Section::make('Additional Documents')
                            ->schema([
                                TextEntry::make('proposal')
                                    ->label('Event Proposal')
                                    ->formatStateUsing(fn () => 'View Proposal')
                                    ->url(fn ($record) => $record->proposal ? asset('storage/'.$record->proposal) : null)
                                    ->openUrlInNewTab()
                                    ->icon('heroicon-m-document-text')
                                    ->badge()
                                    ->color('primary')
                                    ->visible(fn ($record) => filled($record->proposal)),
                            ]),
                    ])->columnSpan(['md' => 1]),
                ])
                    ->columnSpanFull(),

                Section::make('Sessions & Rundown')
                    ->schema([
                        RepeatableEntry::make('rundown')
                            ->hiddenLabel()
                            ->schema([
                                TextEntry::make('data.title')
                                    ->label('Session Title')
                                    ->weight('bold')
                                    ->size('lg')
                                    ->columnSpanFull(),
                                TextEntry::make('data.date')
                                    ->label('Date')
                                    ->date('d M Y')
                                    ->icon('heroicon-m-calendar')
                                    ->visible(fn ($state) => filled($state)),
                                TextEntry::make('data.place')
                                    ->label('Location')
                                    ->icon('heroicon-m-map-pin')
                                    ->visible(fn ($state) => filled($state)),
                                TextEntry::make('data.start_time')
                                    ->label('Start')
                                    ->icon('heroicon-m-clock')
                                    ->visible(fn ($state) => filled($state)),
                                TextEntry::make('data.end_time')
                                    ->label('End')
                                    ->icon('heroicon-m-clock')
                                    ->visible(fn ($state) => filled($state)),
                                TextEntry::make('data.speaker')
                                    ->label('Speaker')
                                    ->icon('heroicon-m-user')
                                    ->badge()
                                    ->color('info')
                                    ->visible(fn ($state) => filled($state)),
                                TextEntry::make('data.description')
                                    ->label('Description')
                                    ->html()
                                    ->prose()
                                    ->columnSpanFull()
                                    ->visible(fn ($state) => filled($state)),
                                TextEntry::make('data.session_files')
                                    ->label('Files & Materials')
                                    ->formatStateUsing(function ($state) {}),
                                RepeatableEntry::make('data.links')
                                    ->label('External Resources')
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

                Section::make('Event Documentation')
                    ->schema([
                        TextEntry::make('event_docs')
                            ->hiddenLabel()
                            ->getStateUsing(function ($record) {
                                $docs = $record->documentation;
                                if (! is_array($docs)) {
                                    return [];
                                }

                                return $docs;
                            })
                            ->formatStateUsing(function ($state) {
                                $url = asset('storage/'.$state);
                                $name = basename($state);

                                return "<a href=\"{$url}\" target=\"_blank\" class=\"text-primary-600 hover:underline flex items-center gap-1\"><svg class=\"w-4 h-4\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z\"></path></svg><span>{$name}</span></a>";
                            })
                            ->html()
                            ->listWithLineBreaks()
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull()
                    ->visible(fn ($record) => filled($record->documentation) && $record->event_date >= now()->startOfDay()),
            ]);
    }
}
