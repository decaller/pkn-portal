<?php

namespace App\Filament\Admin\Resources\EventRegistrations\Pages;

use App\Filament\Admin\Resources\EventRegistrations\EventRegistrationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEventRegistrations extends ListRecords
{
    protected static string $resource = EventRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Manual registration'),
        ];
    }
}
