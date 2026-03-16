<?php

namespace App\Filament\Shared\Widgets;

use App\Models\Document;
use Filament\Actions\Action;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
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
                    ->latest()
                    ->limit(6)
            )
            ->contentGrid([
                'sm' => 1,
                'md' => 2,
                'xl' => 3,
            ])
            ->columns([
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
                        ->formatStateUsing(fn (string $state): string => match (true) {
                            str_contains($state, 'pdf') => 'PDF Document',
                            str_contains($state, 'word') => 'Word Document',
                            str_contains($state, 'excel') || str_contains($state, 'sheet') => 'Spreadsheet',
                            str_contains($state, 'powerpoint') || str_contains($state, 'presentation') => 'Presentation',
                            str_contains($state, 'image') => 'Image',
                            default => 'Other File',
                        }),
                    TextColumn::make('created_at')
                        ->dateTime()
                        ->size('xs')
                        ->color('gray')
                        ->label(__('Added on')),
                ])->space(1)->extraAttributes([
                    'class' => 'p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700',
                ]),
            ])
            ->paginated(false)
            ->emptyStateHeading(__('No featured documents found.'))
            ->actions([
                Action::make('download')
                    ->label(__('Download'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(fn (Document $record) => Storage::disk('public')->download($record->file_path)),
            ])
            ->recordAction('download');
    }
}
