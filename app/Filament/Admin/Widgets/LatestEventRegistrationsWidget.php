<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\EventRegistrations\Tables\EventRegistrationsTable;
use App\Models\EventRegistration;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestEventRegistrationsWidget extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Event Registrations';

    public function table(Table $table): Table
    {
        return EventRegistrationsTable::configure($table)
            ->query(
                EventRegistration::query()
                    // ->where('payment_status', 'pending')
                    ->latest()
                    ->limit(5)
            )
            ->paginated(false);
    }
}
