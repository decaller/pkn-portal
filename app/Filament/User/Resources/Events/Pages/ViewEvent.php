<?php

namespace App\Filament\User\Resources\Events\Pages;

use App\Filament\User\Resources\EventRegistrations\EventRegistrationResource;
use App\Filament\User\Resources\Events\EventResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewEvent extends ViewRecord
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make("register")
                ->label("Register for this event")
                ->icon("heroicon-o-ticket")
                ->color("success")
                ->visible(fn(): bool => $this->record->allow_registration && $this->record->event_date >= now()->toDateString())
                ->url(fn(): string => EventRegistrationResource::getUrl("create", [
                    "event_id" => $this->record->getKey(),
                ])),
        ];
    }
}
