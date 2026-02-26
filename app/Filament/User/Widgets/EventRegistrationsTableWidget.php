<?php

namespace App\Filament\User\Widgets;

use App\Filament\User\Resources\EventRegistrations\Tables\EventRegistrationsTable;
use App\Filament\User\Resources\EventRegistrations\EventRegistrationResource;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class EventRegistrationsTableWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 2;

    protected static ?int $sort = 2;

    protected function getTableHeading(): ?string
    {
        return "My Event Registrations";
    }

    public function table(Table $table): Table
    {
        return EventRegistrationsTable::configure($table)
            ->query(
                EventRegistrationResource::getEloquentQuery()->limit(5)
            )
            ->paginated(false);
    }
}
