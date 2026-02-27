<?php

namespace App\Filament\Admin\Resources\SurveyTemplates\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SurveyTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Repeater::make('questions')
                    ->schema([
                        TextInput::make('question_text')
                            ->required()
                            ->label('Question'),
                        Select::make('type')
                            ->options([
                                'text' => 'Short Text',
                                'textarea' => 'Long Text (Paragraph)',
                                'rating' => 'Rating (1-5)',
                            ])
                            ->required()
                            ->default('text'),
                    ])
                    ->reorderable()
                    ->collapsible()
                    ->columns(2)
                    ->columnSpanFull()
                    ->defaultItems(1),
            ]);
    }
}
