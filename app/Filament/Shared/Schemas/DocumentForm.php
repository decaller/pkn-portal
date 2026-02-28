<?php

namespace App\Filament\Shared\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('General Information'))
                ->schema([
                    TextInput::make('title')->required(),
                    TagsInput::make('tags')
                        ->placeholder(__('Add tags...'))
                        ->suggestions(
                            fn () => \App\Models\Document::pluck('tags')
                                ->flatten()
                                ->unique()
                                ->toArray(),
                        )
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),

            Section::make(__('Extracted Content'))
                ->description(__('Content read by Apache Tika'))
                ->collapsible()
                ->schema([
                    ViewField::make('file_path')
                        ->label(__('File Preview'))
                        ->view('filament.forms.components.document-file-viewer')
                        ->columnSpanFull(),
                    Textarea::make('content')->rows(10)->readOnly(),
                    KeyValue::make('metadata'),
                ])
                ->columnSpanFull(),
        ]);
    }
}
