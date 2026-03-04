<?php

namespace App\Filament\User\Widgets;

use Filament\Widgets\Widget;

class WelcomeWidget extends Widget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 1;

    protected string $view = 'filament.user.widgets.welcome-widget';

    protected function getViewData(): array
    {
        return [
            'lastRegistration' => \App\Models\EventRegistration::with(['participants', 'event'])
                ->where('booker_user_id', auth()->id())
                ->latest()
                ->first(),
        ];
    }
}
