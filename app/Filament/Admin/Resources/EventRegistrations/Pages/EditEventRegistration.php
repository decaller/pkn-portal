<?php

namespace App\Filament\Admin\Resources\EventRegistrations\Pages;

use App\Filament\Admin\Resources\EventRegistrations\EventRegistrationResource;
use App\Filament\Admin\Resources\Users\UserResource;
use App\Models\EventRegistration;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditEventRegistration extends EditRecord
{
    protected static string $resource = EventRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_booker')
                ->label(__('View Booker Profile'))
                ->icon('heroicon-o-user')
                ->color('gray')
                ->url(fn (EventRegistration $record): string => UserResource::getUrl('view', ['record' => $record->booker_user_id])),
        ];
    }
}
