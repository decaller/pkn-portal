<?php

namespace App\Filament\User\Resources\Documents\Pages;

use App\Filament\User\Resources\Documents\DocumentResource;
// No actions needed
use Filament\Resources\Pages\ListRecords;

class ListDocuments extends ListRecords
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
