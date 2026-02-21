<?php

namespace App\Filament\User\Resources\EventRegistrations\Pages;

use App\Filament\User\Resources\EventRegistrations\EventRegistrationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEventRegistration extends ViewRecord
{
    protected static string $resource = EventRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
