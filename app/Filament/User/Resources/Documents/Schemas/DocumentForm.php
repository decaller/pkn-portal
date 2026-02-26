<?php

namespace App\Filament\User\Resources\Documents\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('event_id')
                    ->relationship('event', 'title'),
                TextInput::make('session_slug'),
                TextInput::make('title')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('tags'),
                TextInput::make('file_path')
                    ->required(),
                TextInput::make('original_filename'),
                Textarea::make('content')
                    ->columnSpanFull(),
                TextInput::make('mime_type'),
                TextInput::make('metadata'),
                Textarea::make('description')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
