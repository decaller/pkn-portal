<?php

namespace App\Filament\Resources\News\Pages;

use App\Filament\Resources\News\NewsResource;
use App\Filament\Resources\News\Widgets\TopViewedNews;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;



class ListNews extends ListRecords
{
    protected static string $resource = NewsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            TopViewedNews::class,
        ];
    }
}
