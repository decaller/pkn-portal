<?php

namespace App\Filament\Shared\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;


class NewsInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        \Filament\Infolists\Components\ImageEntry::make('thumbnail')
                            ->hiddenLabel()
                            ->width('100%')
                            ->extraImgAttributes(['class' => 'object-contain rounded-xl w-full']),
                            
                        \Filament\Infolists\Components\TextEntry::make('title')
                            ->hiddenLabel()
                            ->weight('bold')
                            ->size('lg')
                            ->extraAttributes(['class' => 'text-3xl mt-4']),
                            
                        \Filament\Infolists\Components\TextEntry::make('created_at')
                            ->hiddenLabel()
                            ->dateTime('F d, Y')
                            ->color('gray')
                            ->icon('heroicon-m-calendar'),
                            
                        \Filament\Infolists\Components\TextEntry::make('content')
                            ->hiddenLabel()
                            ->html()
                            ->prose()
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make('Related Event')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('event.title')
                            ->label('Event Name')
                            ->weight('bold')
                            ->icon('heroicon-m-calendar-days')
                            ->url(fn ($record) => $record->event_id ? \App\Filament\User\Resources\Events\EventResource::getUrl('view', ['record' => $record->event_id]) : null)
                            ->color('primary')
                            ->size('lg'),
                            
                        Section::make([
                            \Filament\Actions\Action::make('register')
                                ->label('Register Now')
                                ->icon('heroicon-m-ticket')
                                ->color('success')
                                ->url(fn ($record) => \App\Filament\User\Resources\EventRegistrations\EventRegistrationResource::getUrl('create', ['event_id' => $record->event_id]))
                        ])->visible(fn ($record) => filled($record->event_id)),
                    ])
                    ->visible(fn ($record) => filled($record->event_id))
                    ->columnSpanFull(),
            ]);
    }
}
