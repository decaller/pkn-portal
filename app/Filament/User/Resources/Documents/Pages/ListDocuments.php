<?php

namespace App\Filament\User\Resources\Documents\Pages;

use App\Filament\Shared\Widgets\FeaturedDocumentsWidget;
use App\Filament\User\Resources\Documents\DocumentResource;
use Filament\Resources\Pages\ListRecords;

class ListDocuments extends ListRecords
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            FeaturedDocumentsWidget::class,
        ];
    }
}
