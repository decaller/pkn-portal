<?php

namespace App\Filament\User\Widgets;

use App\Filament\User\Resources\EventRegistrations\EventRegistrationResource;
use App\Filament\User\Resources\Events\EventResource;
use App\Models\Event;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Filament\Actions\ViewAction;

class AvailableRegistrationEventsWidget extends TableWidget
{
    protected int|string|array $columnSpan = "full";

    protected static ?int $sort = 2;

    protected static ?string $heading = "Available events to register";

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Event::query()
                    ->where("is_published", true)
                    ->where("allow_registration", true)
                    ->whereDate("event_date", ">=", now()->toDateString())
                    ->orderBy("event_date"),
            )
            ->columns([
                TextColumn::make("title")->label("Event")->weight("bold"),
                TextColumn::make("event_date")
                    ->label("Date")
                    ->date("d M Y")
                    ->sortable(),
            ])
            ->recordUrl(fn(Event $record): string => EventResource::getUrl("view", [
                "record" => $record->getKey(),
            ]))
            ->recordActions([
                ViewAction::make(),
                Action::make("register")->icon("heroicon-o-ticket")->url(
                    fn(
                        Event $record,
                    ): string => EventRegistrationResource::getUrl("create", [
                        "event_id" => $record->getKey(),
                    ]),
                ),
            ])
            ->defaultSort("event_date", "asc")
            ->paginated(false)
            ->emptyStateHeading(
                "No events are open for registration right now.",
            );
    }
}
