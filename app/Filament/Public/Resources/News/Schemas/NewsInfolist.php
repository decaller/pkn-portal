<?php

namespace App\Filament\Public\Resources\News\Schemas;

use App\Filament\Admin\Resources\Events\EventResource;
use Filament\Actions\Action;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

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
                            ->url(fn ($record) => $record->event_id ? EventResource::getUrl('view', ['record' => $record->event_id]) : null)
                            ->color('primary')
                            ->size('lg'),

                        Section::make([
                            Action::make('register')
                                ->label('Register Now')
                                ->icon('heroicon-m-ticket')
                                ->color('success')
                                ->url(fn ($record) => route('filament.user.auth.register', ['event_id' => $record->event_id])),
                        ])->visible(fn ($record) => filled($record->event_id)),
                    ])
                    ->visible(fn ($record) => filled($record->event_id))
                    ->columnSpanFull(),
            ]);
    }
}
