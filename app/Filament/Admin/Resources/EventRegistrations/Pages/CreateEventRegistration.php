<?php

namespace App\Filament\Admin\Resources\EventRegistrations\Pages;

use App\Filament\Admin\Resources\EventRegistrations\EventRegistrationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEventRegistration extends CreateRecord
{
    protected static string $resource = EventRegistrationResource::class;
}
