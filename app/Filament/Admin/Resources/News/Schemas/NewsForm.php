<?php

namespace App\Filament\Admin\Resources\News\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NewsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Main Content')
                    ->description('The primary information for this news post.')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255),

                        RichEditor::make('content')
                            ->required()
                            ->columnSpanFull(),
                    ])->columnSpan(2),

                Section::make('Metadata')
                    ->schema([
                        FileUpload::make('thumbnail')
                            ->image()
                            ->disk('public')
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth('1200')
                            ->directory('news-thumbnails')
                            ->imageEditor(),

                        Toggle::make('is_published')
                            ->label('Visible to Parents')
                            ->default(true),

                        \Filament\Forms\Components\Select::make('event_id')
                            ->label('Related Event')
                            ->relationship('event', 'title')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText('Link this news to a specific event to drive registrations.'),
                    ])->columnSpan(1),
            ]);
    }
}
