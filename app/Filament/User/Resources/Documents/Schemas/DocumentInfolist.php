<?php

namespace App\Filament\User\Resources\Documents\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class DocumentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General Information')
                    ->schema([
                        TextEntry::make('title')
                            ->weight('bold')
                            ->size('lg'),
                        TextEntry::make('tags')
                            ->badge()
                            ->separator(',')
                            ->columnSpanFull(),
                    ])->columnSpanFull(),
                    
                Section::make('Extracted Content')
                    ->description('Content read by Apache Tika')
                    ->collapsible()
                    ->schema([
                        \Filament\Infolists\Components\ViewEntry::make('file_path')
                            ->label('File Preview')
                            ->view('filament.infolists.components.document-file-viewer')
                            ->columnSpanFull(),
                        TextEntry::make('content')
                            ->html()
                            ->prose()
                            ->columnSpanFull(),
                        \Filament\Infolists\Components\KeyValueEntry::make('metadata')
                            ->state(function ($record) {
                                $data = $record->metadata;
                                if (!is_array($data)) return [];
                                $result = [];
                                foreach ($data as $key => $value) {
                                    $result[$key] = is_array($value) ? implode(', ', $value) : $value;
                                }
                                return $result;
                            })
                            ->columnSpanFull(),
                    ])->columnSpanFull(),
            ]);
    }
}
