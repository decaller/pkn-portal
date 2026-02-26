<?php

namespace App\Filament\User\Widgets;

use App\Models\News;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestNewsWidget extends TableWidget
{
    protected int|string|array $columnSpan = 2;

    protected static ?int $sort = 6;

    protected static ?string $heading = "Latest news";

    public function table(Table $table): Table
    {
        return $table
            ->query(
                News::query()->where("is_published", true)->latest()->limit(5),
            )
            ->columns([
                TextColumn::make("title")->weight("bold")->limit(45),
                TextColumn::make("created_at")->since()->label("Published"),
            ])
            ->paginated(false)
            ->emptyStateHeading("No published news yet.");
    }
}
