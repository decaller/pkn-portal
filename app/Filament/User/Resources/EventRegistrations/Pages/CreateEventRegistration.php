<?php

namespace App\Filament\User\Resources\EventRegistrations\Pages;

use App\Filament\User\Resources\EventRegistrations\EventRegistrationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEventRegistration extends CreateRecord
{
    protected static string $resource = EventRegistrationResource::class;
}
