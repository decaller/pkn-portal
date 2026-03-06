<?php

namespace App\Filament\Admin\Resources\News\Pages;

use App\Filament\Admin\Resources\News\NewsResource;
use pxlrbt\FilamentActivityLog\Pages\ListActivities;

class ListNewsActivities extends ListActivities
{
    protected static string $resource = NewsResource::class;
}
