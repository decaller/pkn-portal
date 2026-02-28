<?php

namespace App\Filament\Admin\Resources\Documents\Pages;

use App\Filament\Admin\Resources\Documents\DocumentResource;
use App\Filament\Admin\Resources\Documents\Widgets\TopViewedDocuments;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDocuments extends ListRecords
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TopViewedDocuments::class,
        ];
    }
}
