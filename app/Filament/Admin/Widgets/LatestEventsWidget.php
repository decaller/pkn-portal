<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\User\Resources\Events\Tables\EventsTable;
use App\Models\Event;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestEventsWidget extends TableWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = null;

    public function getHeading(): string
    {
        return __('Latest Events');
    }

    public function table(Table $table): Table
    {
        return EventsTable::configure($table)
            ->query(fn (): Builder => Event::query()->latest())
            ->paginated(false)
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     //
                // ]),
            ]);
    }
}
