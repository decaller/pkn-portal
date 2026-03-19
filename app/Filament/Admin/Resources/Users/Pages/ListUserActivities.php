<?php

namespace App\Filament\Admin\Resources\Users\Pages;

use App\Filament\Admin\Resources\Users\UserResource;
use pxlrbt\FilamentActivityLog\Pages\ListActivities;
use Spatie\Activitylog\Models\Activity;

class ListUserActivities extends ListActivities
{
    protected static string $resource = UserResource::class;

    public function getActivities()
    {
        return $this->paginateQuery(
            Activity::query()
                ->where(function ($query) {
                    $query->where(function ($q) {
                        $q->where('subject_type', $this->record->getMorphClass())
                            ->where('subject_id', $this->record->getKey());
                    })->orWhere(function ($q) {
                        $q->where('causer_type', $this->record->getMorphClass())
                            ->where('causer_id', $this->record->getKey());
                    });
                })
                ->with(['causer', 'subject'])
                ->latest()
        );
    }
}
