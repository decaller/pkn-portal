<?php

namespace App\Filament\User\Widgets;

use App\Filament\User\Resources\Events\EventResource;
use App\Models\Event;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class PastEventsWidget extends TableWidget
{
    protected int|string|array $columnSpan = 2;

    protected static ?int $sort = 5;

    protected static ?string $heading = 'Past events';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Event::query()
                    ->where('is_published', true)
                    ->whereDate('event_date', '<', now()->toDateString())
                    ->latest('event_date')
                    ->limit(5),
            )
            ->columns([
                TextColumn::make('title')->weight('bold')->limit(40),
                TextColumn::make('event_date')->date('d M Y')->sortable(),
            ])
            ->recordUrl(
                fn (Event $record): string => EventResource::getUrl('view', [
                    'record' => $record,
                ]),
            )
            ->paginated(false)
            ->emptyStateHeading('No past events.');
    }
}
