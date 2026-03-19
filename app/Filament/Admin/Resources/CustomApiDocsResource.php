<?php

namespace App\Filament\Admin\Resources;

use ZPMLabs\FilamentApiDocsBuilder\Filament\Resources\ApiDocsResource\ApiDocsResource;

class CustomApiDocsResource extends ApiDocsResource
{
    protected static bool $isScopedToTenant = false;
}
