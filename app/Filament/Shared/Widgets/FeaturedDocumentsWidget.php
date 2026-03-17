<?php

namespace App\Filament\Shared\Widgets;

use App\Filament\Admin\Resources\Documents\DocumentResource;
use App\Models\Document;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Storage;

class FeaturedDocumentsWidget extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 1;

    public function getHeading(): string
    {
        return __('Featured Documents');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Document::query()
                    ->whereJsonContains('tags', 'featured')
                    ->where('is_active', true)
                    ->inRandomOrder()
            )
            ->contentGrid([
                'sm' => 1,
                'md' => 2,
                'xl' => 3,
            ])
            ->columns([
                Stack::make([
                    ViewColumn::make('cover_image')
                        ->view('filament.tables.columns.document-cover-image')
                        ->extraAttributes([
                            'class' => 'w-full h-[200px] rounded-t-xl overflow-hidden',
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
                            ->formatStateUsing(fn (?string $state): string => match (true) {
                                str_contains($state ?? '', 'pdf') => __('PDF Document'),
                                str_contains($state ?? '', 'word') => __('Word Document'),
                                str_contains($state ?? '', 'excel') || str_contains($state ?? '', 'sheet') => __('Spreadsheet'),
                                str_contains($state ?? '', 'powerpoint') || str_contains($state ?? '', 'presentation') => __('Presentation'),
                                str_contains($state ?? '', 'image') => __('Image'),
                                default => __('Other File'),
                            }),
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
            ->paginated([3, 6, 'all'])
            ->emptyStateHeading(__('No featured documents found.'))
            ->actions([
                Action::make('view')
                    ->label(__('View'))
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->url(fn (Document $record): string => match (Filament::getCurrentPanel()?->getId()) {
                        'admin' => DocumentResource::getUrl('view', ['record' => $record]),
                        'user' => \App\Filament\User\Resources\Documents\DocumentResource::getUrl('view', ['record' => $record]),
                        default => '#'
                    }),
                Action::make('download')
                    ->label(__('Download'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(fn (Document $record) => Storage::disk('public')->download($record->file_path)),
            ])
            ->recordUrl(fn (Document $record): string => match (Filament::getCurrentPanel()?->getId()) {
                'admin' => DocumentResource::getUrl('view', ['record' => $record]),
                'user' => \App\Filament\User\Resources\Documents\DocumentResource::getUrl('view', ['record' => $record]),
                default => '#'
            });
    }
}
