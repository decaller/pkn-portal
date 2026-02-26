<?php

namespace App\Filament\Public\Resources\Events\Pages;

use App\Filament\Public\Resources\Events\EventResource;
use Filament\Resources\Pages\ViewRecord;

class ViewEvent extends ViewRecord
{
    protected static string $resource = EventResource::class;
}
