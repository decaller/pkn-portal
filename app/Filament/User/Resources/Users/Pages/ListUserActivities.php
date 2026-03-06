<?php

namespace App\Filament\User\Resources\Users\Pages;

use App\Filament\User\Resources\Users\UserResource;
use App\Models\User;
use Filament\Notifications\Notification;
use pxlrbt\FilamentActivityLog\Pages\ListActivities;

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
}
