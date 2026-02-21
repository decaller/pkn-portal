<?php

namespace App\Filament\Resources\News\Widgets;

use App\Filament\Resources\News\NewsResource;
use App\Models\News;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class TopViewedNews extends TableWidget
{
    // This makes the widget take up the full width of the page
    protected int|string|array $columnSpan = "full";

    // Lower number = appears higher on the page
    protected static ?int $sort = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // The Logic: Get News, count the 'analytics', and sort by that count
                News::query()
                    ->withCount("analytics")
                    ->orderByDesc("analytics_count"),
            )
            ->columns([
                // 1. Thumbnail
                ImageColumn::make("thumbnail")->circular()->label(""),

                // 2. Title
                TextColumn::make("title")
                    ->weight("bold")
                    ->label("Article")
                    ->limit(50),

                // 3. The View Count (The most important part)
                TextColumn::make("analytics_count")
                    ->label("Total Views")
                    ->badge()
                    ->color("success") // Green looks positive for high views
                    ->sortable(),

                // 4. Date
                TextColumn::make("created_at")
                    ->dateTime()
                    ->label("Posted On")
                    ->sortable(),
            ])
            // Let's only show the top 5 so it doesn't clutter the page
            ->paginated(false)
            ->recordUrl(
                fn(News $record): string => NewsResource::getUrl("edit", [
                    "record" => $record,
                ]),
            );
    }
}
