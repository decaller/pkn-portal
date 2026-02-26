<?php

namespace App\Filament\Public\Resources\News\Pages;

use App\Filament\Public\Resources\News\NewsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNews extends CreateRecord
{
    protected static string $resource = NewsResource::class;
}
