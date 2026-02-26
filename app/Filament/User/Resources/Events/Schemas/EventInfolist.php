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
                Grid::make(['default' => 1, 'md' => 3])->schema([
                    Group::make([
                        Section::make('Event Guidelines')
                            ->schema([
                                TextEntry::make('title')->weight('bold')->size('lg'),
                                TextEntry::make('description')->markdown()->prose(),
                                TextEntry::make('event_date')->date('d M Y')->icon('heroicon-m-calendar-days'),
                            ]),
                    ])->columnSpan(['md' => 2]),

                    Group::make([
                        Section::make('Media')
                            ->schema([
                                ImageEntry::make('cover_image')->hiddenLabel(),
                                ImageEntry::make('photos')
                                    ->hiddenLabel()
                                    ->stacked()
                                    ->circular(),
                            ]),
                        Section::make('Rundown')
                            ->schema([
                                RepeatableEntry::make('rundown')
                                    ->hiddenLabel()
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextEntry::make('start_time')->label('Start')->icon('heroicon-m-clock'),
                                            TextEntry::make('activity')->label('Activity'),
                                        ]),
                                    ]),
                            ])
                            ->visible(fn ($record) => filled($record->rundown)),
                    ])->columnSpan(['md' => 1]),
                ]),
            ]);
    }
}
