<?php

namespace App\Filament\User\Resources\Users\Pages;

use App\Filament\User\Resources\Users\UserResource;
use App\Models\User;
use Filament\Notifications\Notification;
use pxlrbt\FilamentActivityLog\Pages\ListActivities;
use Spatie\Activitylog\Models\Activity;

class ListUserActivities extends ListActivities
{
    protected static string $resource = UserResource::class;

    public function mount($record): void
    {
        parent::mount($record);

        /** @var User $target */
        $target = $this->record;

        if (! UserResource::canViewActivities($target)) {
            Notification::make()
                ->title(__('Access denied'))
                ->danger()
                ->send();

            $this->redirect(UserResource::getUrl('index'));
        }
    }

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
