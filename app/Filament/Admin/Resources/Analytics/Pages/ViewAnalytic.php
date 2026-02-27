<?php

namespace App\Filament\Admin\Resources\Analytics\Pages;

use App\Filament\Admin\Resources\Analytics\AnalyticResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAnalytic extends ViewRecord
{
    protected static string $resource = AnalyticResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
