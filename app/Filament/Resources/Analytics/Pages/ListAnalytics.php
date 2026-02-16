<?php

namespace App\Filament\Resources\Analytics\Pages;

use App\Filament\Resources\Analytics\AnalyticResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\News\Widgets\TopViewedNews;
use App\Filament\Resources\Events\Widgets\TopViewedEvents;

class ListAnalytics extends ListRecords
{
    protected static string $resource = AnalyticResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            TopViewedNews::class,
            TopViewedEvents::class,
        ];
    }
}
