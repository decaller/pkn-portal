<?php

namespace App\Filament\Shared\Widgets;

use App\Filament\Admin\Resources\Documents\DocumentResource as AdminDocumentResource;
use App\Filament\Admin\Resources\Events\EventResource as AdminEventResource;
use App\Filament\Public\Resources\Events\EventResource as PublicEventResource;
use App\Filament\User\Resources\Documents\DocumentResource as UserDocumentResource;
use App\Models\Document;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentGridTableWidget extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = null;

    public ?int $recordId = null;

    public string $mode = 'similar';

    protected ?Document $record = null;

    public function mount(?int $recordId = null, string $mode = 'similar'): void
    {
        $this->recordId = $recordId;
        $this->mode = $mode;
        $this->record = $recordId ? Document::query()->find($recordId) : null;
        static::$heading = $this->mode === 'related'
            ? __('Other Files From This Event')
            : __('Similar Files');
    }

    public function table(Table $table): Table
    {
        $query = Document::query()->with('event');

        if (! $this->record) {
            $query->whereRaw('1 = 0');
        } elseif ($this->mode === 'related') {
            if (! filled($this->record->event_id)) {
                $query->whereRaw('1 = 0');
            } else {
                $query
                    ->whereKeyNot($this->record->getKey())
                    ->where('event_id', $this->record->event_id);
            }
        } else {
            $keywords = self::buildSimilarityKeywords($this->record);

            if (empty($keywords)) {
                $query->whereRaw('1 = 0');
            } else {
                $likeOperator = self::similarityLikeOperator();

                $query
                    ->whereKeyNot($this->record->getKey())
                    ->where(function (Builder $query) use ($keywords, $likeOperator): void {
                        foreach ($keywords as $keyword) {
                            $query
                                ->orWhere('title', $likeOperator, "%{$keyword}%")
                                ->orWhere('content', $likeOperator, "%{$keyword}%");
                        }
                    });
            }
        }

        $isRelated = $this->mode === 'related';
        $emptyHeading = $isRelated
            ? __('No other files found for this event.')
            : __('No similar files found.');

        $contentGrid = ['sm' => 1, 'md' => 3];

        $query = $query->latest()->limit(3);

        $table = $table
            ->query($query)
            ->contentGrid($contentGrid)
            ->columns([
                Stack::make([
                    ViewColumn::make('cover_image')
                        ->view('filament.tables.columns.document-cover-image')
                        ->extraAttributes([
                            'class' => 'w-full h-40 object-cover rounded-t-xl overflow-hidden',
                        ]),
                    Stack::make([
                        TextColumn::make('title')
                            ->weight('bold')
                            ->size('lg')
                            ->limit(60)
                            ->extraAttributes([
                                'class' => 'text-primary-600 dark:text-primary-400',
                            ]),
                        TextColumn::make('mime_type')
                            ->color('gray')
                            ->size('sm')
                            ->formatStateUsing(fn (?string $state): string => self::formatMimeType($state)),
                        TextColumn::make('event.title')
                            ->label(__('Event'))
                            ->color('gray')
                            ->size('xs'),
                        TextColumn::make('created_at')
                            ->dateTime()
                            ->size('xs')
                            ->color('gray')
                            ->label(__('Added on')),
                    ])->space(1)->extraAttributes([
                        'class' => 'p-4',
                    ]),
                ])->space(0)->extraAttributes([
                    'class' => 'bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 h-full overflow-hidden transition-all hover:ring-2 hover:ring-primary-500',
                ]),
            ])
            ->actions([
                Action::make('view')
                    ->label(__('View'))
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->url(fn (Document $record): string => self::documentUrl($record)),
                Action::make('download')
                    ->label(__('Download'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(fn (Document $record) => Storage::disk('public')->download($record->file_path)),
            ])
            ->recordUrl(fn (Document $record): string => self::documentUrl($record))
            ->emptyStateHeading($emptyHeading)
            ->headerActions($this->headerActions());

        return $table->paginated(false);
    }

    /**
     * @return array<int, Action>
     */
    private function headerActions(): array
    {
        if ($this->mode !== 'related') {
            return [];
        }

        $eventUrl = self::eventUrl($this->record?->event_id);

        if (! $eventUrl) {
            return [];
        }

        return [
            Action::make('view_event')
                ->label(__('View Related Event'))
                ->icon('heroicon-o-link')
                ->color('info')
                ->url($eventUrl),
        ];
    }

    private static function buildSimilarityKeywords(Document $record): array
    {
        $source = trim(($record->title ?? '').' '.($record->content ?? ''));

        if ($source === '') {
            return [];
        }

        $normalized = Str::lower($source);
        $normalized = preg_replace('/[^a-z0-9\s]+/u', ' ', $normalized) ?? '';
        $tokens = preg_split('/\s+/', $normalized, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        $tokens = array_values(array_unique(array_filter(
            $tokens,
            static fn (string $token): bool => strlen($token) >= 4,
        )));

        return array_slice($tokens, 0, 8);
    }

    private static function similarityLikeOperator(): string
    {
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        return $driver === 'pgsql' ? 'ilike' : 'like';
    }

    private static function documentUrl(Document $document): string
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
