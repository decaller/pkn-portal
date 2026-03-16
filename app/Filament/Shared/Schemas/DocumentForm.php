<?php

namespace App\Filament\Shared\Schemas;

use App\Models\Document;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
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
            Section::make(__('Upload Document'))
                ->description(__('Upload a new document manually.'))
                ->schema([
                    FileUpload::make('file_path')
                        ->label(__('File'))
                        ->disk('public')
                        ->directory('manual-uploads')
                        ->required()
                        ->preserveFilenames()
                        ->openable()
                        ->downloadable()
                        ->maxSize(1048576)
                        ->helperText(__('Max 1GB. Supported formats: PDF, PPTX, DOCX, XLSX, TXT, Images.')),
                ])
                ->visible(fn ($record) => $record === null || ! $record->exists),

            Section::make(__('General Information'))
                ->schema([
                    TextInput::make('title')
                        ->required()
                        ->helperText(__('Enter a descriptive title for this document.')),

                    Select::make('event_id')
                        ->relationship('event', 'title')
                        ->label(__('Related Event'))
                        ->placeholder(__('Select an event (optional)'))
                        ->searchable()
                        ->preload()
                        ->nullable(),

                    TagsInput::make('tags')
                        ->placeholder(__('Add tags...'))
                        ->suggestions(
                            fn () => Document::pluck('tags')
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
                    ViewField::make('file_path_preview')
                        ->label(__('File Preview'))
                        ->view('filament.forms.components.document-file-viewer')
                        ->formatStateUsing(fn ($record) => $record?->file_path)
                        ->columnSpanFull()
                        ->visible(fn ($record) => $record !== null)
                        ->dehydrated(false),
                    Textarea::make('content')->rows(10)->readOnly(),
                    KeyValue::make('metadata'),
                ])
                ->columnSpanFull(),
        ]);
    }
}
