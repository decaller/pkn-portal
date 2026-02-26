<?php

namespace App\Filament\Widgets;

use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LatestEventsWidget extends TableWidget
{
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 'full';
    public function table(Table $table): Table
    {
        return \App\Filament\User\Resources\Events\Tables\EventsTable::configure($table)
            ->query(fn (): \Illuminate\Database\Eloquent\Builder => \App\Models\Event::query()->latest())
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
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
