<?php

namespace App\Filament\User\Resources\Documents\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DocumentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('event.title')
                    ->label('Event')
                    ->placeholder('-'),
                TextEntry::make('session_slug')
                    ->placeholder('-'),
                TextEntry::make('title'),
                TextEntry::make('slug'),
                TextEntry::make('file_path'),
                TextEntry::make('original_filename')
                    ->placeholder('-'),
                TextEntry::make('content')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('mime_type')
                    ->placeholder('-'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
