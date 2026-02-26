<?php

namespace App\Filament\Widgets;

use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use App\Models\EventRegistration;

class LatestEventRegistrationsWidget extends TableWidget
{
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $heading = 'Event Registrations';

    public function table(Table $table): Table
    {
        return \App\Filament\Resources\EventRegistrations\Tables\EventRegistrationsTable::configure($table)
            ->query(
                EventRegistration::query()
                    // ->where('payment_status', 'pending')
                    ->latest()
                    ->limit(5)
            )
            ->paginated(false);
    }
}
