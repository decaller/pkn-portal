<?php

namespace App\Filament\Resources\Events\Schemas;

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
                                    ->url(fn ($record) => $record->proposal ? asset('storage/' . $record->proposal) : null)
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
                                RepeatableEntry::make('data.session_files')
                                    ->label('Files & Materials')
                                    ->getStateUsing(function ($state) {
                                        if (!is_array($state)) return [];
                                        return array_map(fn($file) => ['file_path' => $file], $state);
                                    })
                                    ->schema([
                                        TextEntry::make('file_path')
                                            ->hiddenLabel()
                                            ->formatStateUsing(fn ($state) => basename($state))
                                            ->icon('heroicon-o-document-text')
                                            ->url(fn ($state) => asset('storage/' . $state))
                                            ->openUrlInNewTab(),
                                    ])
                                    ->grid(2)
                                    ->columnSpanFull()
                                    ->visible(fn ($state) => filled($state)),
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
                        RepeatableEntry::make('documentation')
                            ->hiddenLabel()
                            ->getStateUsing(function ($record) {
                                $docs = $record->documentation;
                                if (!is_array($docs)) return [];
                                return array_map(fn($file) => ['file_path' => $file], $docs);
                            })
                            ->schema([
                                TextEntry::make('file_path')
                                    ->hiddenLabel()
                                    ->formatStateUsing(fn ($state) => basename($state))
                                    ->icon('heroicon-o-document-text')
                                    ->url(fn ($state) => asset('storage/' . $state))
                                    ->openUrlInNewTab(),
                            ])
                            ->grid(2)
                    ])
                    ->columnSpanFull()
                    ->visible(fn ($record) => filled($record->documentation)),
                    
                Section::make('Testimonials')
                    ->schema([
                        RepeatableEntry::make('approvedTestimonials')
                            ->hiddenLabel()
                            ->schema([
                                TextEntry::make('author')
                                    ->getStateUsing(fn ($record) => $record->user ? $record->user->name : $record->guest_name)
                                    ->label('Participant')
                                    ->weight('bold')
                                    ->icon('heroicon-m-user'),
                                TextEntry::make('rating')
                                    ->badge()
                                    ->color('warning')
                                    ->icon('heroicon-m-star'),
                                TextEntry::make('content')
                                    ->hiddenLabel()
                                    ->prose()
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->grid(2),
                    ])
                    ->columnSpanFull()
                    ->visible(fn ($record) => $record->approvedTestimonials()->exists()),
                    
                Section::make('What People Say About Us')
                    ->schema([
                        RepeatableEntry::make('previous_testimonials') // State-driven component
                            ->hiddenLabel()
                            ->state(function (\App\Models\Event $record) {
                                return \App\Models\Testimonial::with(['user', 'event'])
                                    ->where('event_id', '!=', $record->id)
                                    ->where('is_approved', true)
                                    ->where('rating', '>=', 4)
                                    ->inRandomOrder()
                                    ->limit(4)
                                    ->get();
                            })
                            ->schema([
                                TextEntry::make('event.title')
                                    ->label('From Event')
                                    ->icon('heroicon-m-calendar')
                                    ->color('primary')
                                    ->columnSpanFull(),
                                TextEntry::make('author')
                                    ->label('Participant')
                                    ->getStateUsing(fn ($record) => $record->user ? $record->user->name : $record->guest_name)
                                    ->weight('bold')
                                    ->icon('heroicon-m-user'),
                                TextEntry::make('rating')
                                    ->badge()
                                    ->color('warning')
                                    ->icon('heroicon-m-star'),
                                TextEntry::make('content')
                                    ->hiddenLabel()
                                    ->prose()
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->grid(2),
                    ])
                    ->columnSpanFull()
                    ->visible(fn ($record) => $record->allow_registration && $record->event_date >= now()->startOfDay()),
            ]);
    }
}
