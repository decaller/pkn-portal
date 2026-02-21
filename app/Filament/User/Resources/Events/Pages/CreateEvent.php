<?php

namespace App\Filament\User\Resources\Events\Pages;

use App\Filament\User\Resources\Events\EventResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEvent extends CreateRecord
{
    protected static string $resource = EventResource::class;
}
