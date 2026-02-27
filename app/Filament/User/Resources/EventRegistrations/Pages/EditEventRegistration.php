<?php

namespace App\Filament\User\Resources\EventRegistrations\Pages;

use App\Filament\User\Resources\EventRegistrations\EventRegistrationResource;
use Filament\Resources\Pages\EditRecord;

class EditEventRegistration extends EditRecord
{
    protected static string $resource = EventRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('view', [
            'record' => $this->record,
        ]);
    }
}
