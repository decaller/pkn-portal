<?php

namespace App\Filament\User\Resources\News\Pages;

use App\Filament\User\Resources\News\NewsResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewNews extends ViewRecord
{
    protected static string $resource = NewsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
