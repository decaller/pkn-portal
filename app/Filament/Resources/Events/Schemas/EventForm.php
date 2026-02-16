<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Illuminate\Support\Str;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Event Identity')
                    ->description('Basic information and folder naming.')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->live(onBlur: true) // Updates the slug when you click away
                            ->afterStateUpdated(fn (string $operation, $state, $set) => 
                                $operation === 'create' ? $set('slug', Str::slug($state)) : null
                            ),

                        TextInput::make('slug')
                            ->disabled() // Keep it disabled so users don't break the folder link
                            ->dehydrated() // Ensures it still gets saved to the DB
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->label('Folder Name (Auto-generated)'),
                        
                        DatePicker::make('event_date')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                    ])->columnSpan(2),

                Section::make('Media')
                    ->schema([
                        FileUpload::make('cover_image')
                            ->image()
                            ->directory('event-covers'), // Cover image goes in a generic folder
                    ])->columnSpan(1),

                Section::make('Description')
                    ->schema([
                        RichEditor::make('description')
                            ->columnSpanFull(),
                    ])->columnSpanFull(),
            ]);
    }
}
