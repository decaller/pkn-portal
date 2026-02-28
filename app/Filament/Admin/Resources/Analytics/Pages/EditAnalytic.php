<?php

namespace App\Filament\Admin\Resources\Analytics\Pages;

use App\Filament\Admin\Resources\Analytics\AnalyticResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAnalytic extends EditRecord
{
    protected static string $resource = AnalyticResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
