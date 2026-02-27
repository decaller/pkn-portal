<?php

namespace App\Filament\Admin\Resources\Documents\Widgets;

use App\Filament\Admin\Resources\Documents\DocumentResource;
use App\Models\Document;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class TopViewedDocuments extends TableWidget
{
    // This makes the widget take up the full width of the page
    protected int|string|array $columnSpan = 'full';

    // Appears right alongside or below the Events widget depending on sort
    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Matching your pattern: counting the analytics relationship
                Document::query()
                    ->withCount('analytics')
                    ->orderByDesc('analytics_count')
                    ->limit(5),
            )
            ->columns([
                // 1. File Type Icon (Better than a blank image for docs)
                IconColumn::make('mime_type')
                    ->label('')
                    ->icon(
                        fn (string $state): string => match ($state) {
                            'application/pdf' => 'heroicon-o-document-text',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'heroicon-o-document',
                            default => 'heroicon-o-paper-clip',
                        },
                    )
                    ->color(
                        fn (string $state): string => match ($state) {
                            'application/pdf' => 'danger',
                            default => 'info',
                        },
                    ),

                // 2. Document Title
                TextColumn::make('title')
                    ->weight('bold')
                    ->label('Document Title')
                    ->description(
                        fn ($record) => $record->event?->title ??
                            'General Archive',
                    )
                    ->limit(50),

                // 3. Tags (Badges)
                TextColumn::make('tags')
                    ->badge()
                    ->separator(',')
                    ->color('info')
                    ->label('Tags'),

                // 4. Analytics Count (Popularity)
                TextColumn::make('analytics_count')
                    ->label('Total Views')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Indexed On')
                    ->sortable(),
            ])
            ->paginated(false)
            ->recordUrl(
                // Links to the slide-over/view page we set up
                fn (Document $record): string => DocumentResource::getUrl(
                    'view',
                    [
                        'record' => $record,
                    ],
                ),
            );
    }
}
