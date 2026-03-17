<?php

namespace App\Filament\Shared\Schemas;

use App\Filament\Admin\Resources\Events\EventResource as AdminEventResource;
use App\Filament\Public\Resources\Events\EventResource as PublicEventResource;
use App\Models\Document;
use Filament\Facades\Filament;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DocumentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('File Preview'))
                    ->schema([
                        ViewEntry::make('file_path')
                            ->hiddenLabel()
                            ->view('filament.infolists.components.document-file-viewer')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make(__('File Information'))
                    ->schema([
                        TextEntry::make('title')
                            ->weight('bold')
                            ->size('lg')
                            ->columnSpanFull(),
                        ImageEntry::make('cover_image')
                            ->label(__('Cover Image'))
                            ->disk('public')
                            ->visibility('public')
                            ->columnSpanFull()
                            ->visible(fn ($record) => filled($record->cover_image)),
                        TextEntry::make('description')
                            ->label(__('Description'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('original_filename')
                            ->label(__('Original filename'))
                            ->placeholder('-'),
                        TextEntry::make('mime_type')
                            ->label(__('Type'))
                            ->badge()
                            ->color('info')
                            ->formatStateUsing(fn (?string $state): string => self::formatMimeType($state)),
                        TextEntry::make('tags')
                            ->label(__('Keywords'))
                            ->badge()
                            ->visible(fn ($state) => filled($state)),
                        TextEntry::make('event.title')
                            ->label(__('Related Event'))
                            ->url(fn (Document $record): ?string => self::eventUrl($record->event_id))
                            ->color('primary')
                            ->visible(fn (Document $record): bool => filled($record->event_id)),
                        TextEntry::make('created_at')
                            ->label(__('Uploaded at'))
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label(__('Last updated'))
                            ->dateTime(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                ViewEntry::make('similar_documents')
                    ->hiddenLabel()
                    ->state(fn (Document $record): array => [
                        'record_id' => $record->getKey(),
                        'mode' => 'similar',
                    ])
                    ->columnSpanFull()
                    ->view('filament.infolists.components.document-grid-table-widget'),

                ViewEntry::make('related_documents')
                    ->hiddenLabel()
                    ->state(fn (Document $record): array => [
                        'record_id' => $record->getKey(),
                        'mode' => 'related',
                    ])
                    ->visible(fn (Document $record): bool => filled($record->event_id))
                    ->columnSpanFull()
                    ->view('filament.infolists.components.document-grid-table-widget'),

                Section::make(__('Extracted Content (Tika)'))
                    ->description(__('Technical metadata and full text identified by Apache Tika.'))
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextEntry::make('content')
                            ->label(__('Extracted Text'))
                            ->markdown()
                            ->prose()
                            ->columnSpanFull()
                            ->visible(fn ($state) => filled($state)),
                        TextEntry::make('metadata')
                            ->label(__('Technical Metadata'))
                            ->formatStateUsing(function ($state) {
                                if (empty($state)) {
                                    return '-';
                                }

                                return '<pre class="text-xs overflow-auto max-h-60 p-4 bg-gray-100 dark:bg-gray-800 rounded-lg">' .
                                    e(json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) .
                                    '</pre>';
                            })
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn () => Filament::getCurrentPanel()?->getId() === 'admin')
                    ->columnSpanFull(),
            ]);
    }

    private static function eventUrl(?int $eventId): ?string
    {
        if (! $eventId) {
            return null;
        }

        $panelId = Filament::getCurrentPanel()?->getId();

        return match ($panelId) {
            'admin' => AdminEventResource::getUrl('view', ['record' => $eventId]),
            'user' => PublicEventResource::getUrl('view', ['record' => $eventId]),
            'public' => PublicEventResource::getUrl('view', ['record' => $eventId]),
            default => PublicEventResource::getUrl('view', ['record' => $eventId]),
        };
    }

    private static function formatMimeType(?string $mimeType): string
    {
        if (! filled($mimeType)) {
            return __('Other File');
        }

        return match (true) {
            str_contains($mimeType, 'pdf') => __('PDF Document'),
            str_contains($mimeType, 'word') => __('Word Document'),
            str_contains($mimeType, 'excel') || str_contains($mimeType, 'sheet') => __('Spreadsheet'),
            str_contains($mimeType, 'powerpoint') || str_contains($mimeType, 'presentation') => __('Presentation'),
            str_contains($mimeType, 'image') => __('Image'),
            default => __('Other File'),
        };
    }
}
