<?php

namespace App\Filament\Public\Resources\News\Pages;

use App\Filament\Public\Resources\News\NewsResource;
use Filament\Resources\Pages\ListRecords;

class ListNews extends ListRecords
{
    protected static string $resource = NewsResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
