<?php

namespace App\Filament\User\Widgets;

use Filament\Widgets\Widget;

class WelcomeWidget extends Widget
{
    protected int|string|array $columnSpan = "full";

    protected static ?int $sort = 1;

    protected string $view = "filament.user.widgets.welcome-widget";
}
