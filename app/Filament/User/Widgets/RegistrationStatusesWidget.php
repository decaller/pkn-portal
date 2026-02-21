<?php

namespace App\Filament\User\Widgets;

use App\Filament\User\Resources\EventRegistrations\EventRegistrationResource;
use App\Models\EventRegistration;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RegistrationStatusesWidget extends TableWidget
{
    protected int|string|array $columnSpan = "full";

    protected static ?int $sort = 3;

    protected static ?string $heading = "My registration statuses";

    public function table(Table $table): Table
    {
        return $table
            ->query(
                EventRegistration::query()
                    ->where("booker_user_id", auth()->id())
                    ->with("event")
                    ->latest(),
            )
            ->columns([
                TextColumn::make("event.title")->label("Event")->weight("bold"),
                TextColumn::make("status")->badge(),
                TextColumn::make("payment_status")->badge(),
                TextColumn::make("updated_at")->since()->label("Updated"),
            ])
            ->recordUrl(
                fn(
                    EventRegistration $record,
                ): string => EventRegistrationResource::getUrl("view", [
                    "record" => $record,
                ]),
            )
            ->defaultSort("created_at", "desc")
            ->paginated(false)
            ->emptyStateHeading("You have no event registrations yet.");
    }
}
