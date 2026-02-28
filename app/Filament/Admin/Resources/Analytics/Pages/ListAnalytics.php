<?php

namespace App\Filament\Admin\Resources\Analytics\Pages;

use App\Filament\Admin\Resources\Analytics\AnalyticResource;
use App\Filament\Admin\Resources\Documents\Widgets\TopViewedDocuments;
use App\Filament\Admin\Resources\Events\Widgets\TopViewedEvents;
use App\Filament\Admin\Resources\News\Widgets\TopViewedNews;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

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
            TopViewedDocuments::class,
        ];
    }
}
