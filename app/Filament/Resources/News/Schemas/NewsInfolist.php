<?php

namespace App\Filament\Resources\News\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;

class NewsInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        ImageEntry::make('thumbnail')
                            ->hiddenLabel()
                            ->width('100%')
                            ->extraImgAttributes(['class' => 'object-contain rounded-xl w-full']),
                            
                        TextEntry::make('title')
                            ->hiddenLabel()
                            ->weight('bold')
                            ->size('lg')
                            ->extraAttributes(['class' => 'text-3xl mt-4']),
                            
                        TextEntry::make('created_at')
                            ->hiddenLabel()
                            ->dateTime('F d, Y')
                            ->color('gray')
                            ->icon('heroicon-m-calendar'),
                            
                        TextEntry::make('content')
                            ->hiddenLabel()
                            ->html()
                            ->prose()
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make('Related Event')
                    ->schema([
                        TextEntry::make('event.title')
                            ->label('Event Name')
                            ->weight('bold')
                            ->icon('heroicon-m-calendar-days')
                            ->url(fn ($record) => $record->event_id ? \App\Filament\Resources\Events\EventResource::getUrl('view', ['record' => $record->event_id]) : null)
                            ->color('primary')
                            ->size('lg'),
                    ])
                    ->visible(fn ($record) => filled($record->event_id))
                    ->columnSpanFull(),
            ]);
    }
}
