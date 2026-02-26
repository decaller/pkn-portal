<?php

namespace App\Filament\User\Resources\News\Pages;

use App\Filament\User\Resources\News\NewsResource;
// No actions needed
use Filament\Resources\Pages\ListRecords;

class ListNews extends ListRecords
{
    protected static string $resource = NewsResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
