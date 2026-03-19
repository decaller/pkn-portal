<?php

namespace App\Filament\Admin\Resources\Events\Pages;

use App\Filament\Admin\Resources\Events\EventResource;
use pxlrbt\FilamentActivityLog\Pages\ListActivities;

class ListEventActivities extends ListActivities
{
    protected static string $resource = EventResource::class;

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
