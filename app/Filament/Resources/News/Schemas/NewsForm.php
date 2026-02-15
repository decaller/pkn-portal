<?php

namespace App\Filament\Resources\News\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
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
                            ->directory('news-thumbnails')
                            ->imageEditor(),
                        
                        Toggle::make('is_published')
                            ->label('Visible to Parents')
                            ->default(true),
                    ])->columnSpan(1),
            ]);
    }
}
