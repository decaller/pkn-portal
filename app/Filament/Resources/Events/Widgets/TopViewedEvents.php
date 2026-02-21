<?php

namespace App\Filament\Resources\Events\Widgets;

use App\Filament\Resources\Events\EventResource;
use App\Models\Event;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

class TopViewedEvents extends TableWidget
{
    // This makes the widget take up the full width of the page
    protected int|string|array $columnSpan = "full";

    // Lower number = appears higher on the page
    protected static ?int $sort = 1;
    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Count views and sort by popularity
                Event::query()
                    ->withCount("analytics")
                    ->orderByDesc("analytics_count"),
            )
            ->columns([
                // 1. Cover Image
                ImageColumn::make("cover_image")->circular()->label(""),

                // 2. Event Title
                TextColumn::make("title")
                    ->weight("bold")
                    ->label("Event Name")
                    ->limit(50),

                // 4. View Count
                TextColumn::make("analytics_count")
                    ->label("Total Views")
                    ->badge()
                    ->color("success")
                    ->sortable(),

                TextColumn::make("created_at")
                    ->dateTime()
                    ->label("Posted On")
                    ->sortable(),
            ])
            ->paginated(false)
            ->recordUrl(
                fn(Event $record): string => EventResource::getUrl("view", [
                    "record" => $record,
                ]),
            );
    }
}
