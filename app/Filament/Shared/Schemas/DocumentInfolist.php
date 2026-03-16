<?php

namespace App\Filament\Shared\Schemas;

use App\Filament\Admin\Resources\Documents\DocumentResource as AdminDocumentResource;
use App\Filament\Admin\Resources\Events\EventResource as AdminEventResource;
use App\Filament\Public\Resources\Events\EventResource as PublicEventResource;
use App\Filament\User\Resources\Documents\DocumentResource as UserDocumentResource;
use App\Models\Document;
use Filament\Facades\Filament;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

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
                            ->separator(',')
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
                        TextEntry::make('content')
                            ->label(__('Extracted Text'))
                            ->markdown()
                            ->prose()
                            ->columnSpanFull()
                            ->visible(fn ($state) => filled($state)),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make(__('Similar Files'))
                    ->description(__('Files with the same type (from Tika).'))
                    ->schema([
                        ViewEntry::make('similar_documents')
                            ->hiddenLabel()
                            ->state(fn (Document $record): array => self::buildSimilarDocumentsState($record))
                            ->view('filament.infolists.components.document-grid-widget'),
                    ])
                    ->columnSpanFull(),

                Section::make(__('Other Files From This Event'))
                    ->schema([
                        ViewEntry::make('event_documents')
                            ->hiddenLabel()
                            ->state(fn (Document $record): array => self::buildEventDocumentsState($record))
                            ->view('filament.infolists.components.document-grid-widget'),
                    ])
                    ->visible(fn (Document $record): bool => filled($record->event_id))
                    ->columnSpanFull(),
            ]);
    }

    private static function buildSimilarDocumentsState(Document $record): array
    {
        if (! filled($record->mime_type)) {
            return self::emptyGridState(__('No similar files found.'));
        }

        $query = Document::query()
            ->whereKeyNot($record->getKey())
            ->where('mime_type', $record->mime_type)
            ->latest();

        return self::buildDocumentGridState(
            query: $query,
            pageParam: 'similar_page',
            emptyLabel: __('No similar files found.'),
        );
    }

    private static function buildEventDocumentsState(Document $record): array
    {
        if (! filled($record->event_id)) {
            return self::emptyGridState(__('No related event files found.'));
        }

        $query = Document::query()
            ->whereKeyNot($record->getKey())
            ->where('event_id', $record->event_id)
            ->latest();

        return self::buildDocumentGridState(
            query: $query,
            pageParam: 'related_page',
            emptyLabel: __('No other files found for this event.'),
            eventUrl: self::eventUrl($record->event_id),
            eventLabel: __('View Related Event'),
        );
    }

    private static function buildDocumentGridState(
        Builder $query,
        string $pageParam,
        string $emptyLabel,
        ?string $eventUrl = null,
        ?string $eventLabel = null,
    ): array {
        $perPage = 6;
        $page = max(1, (int) request()->query($pageParam, 1));

        $total = (clone $query)->count();
        $items = (clone $query)
            ->with('event')
            ->forPage($page, $perPage)
            ->get()
            ->map(fn (Document $document): array => self::mapDocumentItem($document))
            ->all();

        $hasNext = ($page * $perPage) < $total;
        $prevUrl = $page > 1 ? self::buildPageUrl($pageParam, $page - 1) : null;
        $nextUrl = $hasNext ? self::buildPageUrl($pageParam, $page + 1) : null;

        return [
            'items' => $items,
            'empty' => $emptyLabel,
            'prev_url' => $prevUrl,
            'next_url' => $nextUrl,
            'event_url' => $eventUrl,
            'event_label' => $eventLabel,
        ];
    }

    private static function mapDocumentItem(Document $document): array
    {
        return [
            'title' => $document->title,
            'type' => self::formatMimeType($document->mime_type),
            'event' => $document->event?->title,
            'created_at' => $document->created_at?->format('d M Y'),
            'view_url' => self::documentUrl($document),
            'download_url' => Storage::disk('public')->url($document->file_path),
        ];
    }

    private static function buildPageUrl(string $pageParam, int $page): string
    {
        $query = request()->query();
        $query[$pageParam] = $page;

        return url()->current().'?'.http_build_query($query);
    }

    private static function emptyGridState(string $label): array
    {
        return [
            'items' => [],
            'empty' => $label,
            'prev_url' => null,
            'next_url' => null,
        ];
    }

    private static function documentUrl(Document $document): ?string
    {
        $panelId = Filament::getCurrentPanel()?->getId();

        return match ($panelId) {
            'admin' => AdminDocumentResource::getUrl('view', ['record' => $document]),
            'user' => UserDocumentResource::getUrl('view', ['record' => $document]),
            default => AdminDocumentResource::getUrl('view', ['record' => $document]),
        };
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
