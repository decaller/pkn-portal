<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\PublicPanelProvider;
use App\Providers\Filament\UserPanelProvider;
use App\Providers\HorizonServiceProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    PublicPanelProvider::class,
    UserPanelProvider::class,
    HorizonServiceProvider::class,
];
