<?php

namespace App\Filament\Admin\Resources\EventRegistrations\Pages;

use App\Filament\Admin\Resources\EventRegistrations\EventRegistrationResource;
use pxlrbt\FilamentActivityLog\Pages\ListActivities;

class ListEventRegistrationActivities extends ListActivities
{
    protected static string $resource = EventRegistrationResource::class;

    public function getActivities()
    {
        return $this->paginateQuery(
            $this->record->activities()
                ->with(['causer', 'subject'])
                ->latest()
                ->getQuery()
        );
    }
}
