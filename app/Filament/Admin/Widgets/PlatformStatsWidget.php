<?php

namespace App\Filament\Admin\Widgets;

use App\Enums\PaymentStatus;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PlatformStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $tenant = Filament::getTenant();

        $activeEvents = Event::where('allow_registration', true)
            ->where('event_date', '>=', now())
            ->count();

        $pendingRegistrations = EventRegistration::where('payment_status', PaymentStatus::Submitted)
            ->count();

        $totalUsers = $tenant ? $tenant->users()->count() : User::count();

        return [
            Stat::make(__('Active Future Events'), $activeEvents)
                ->description(__('Published events allowing registration'))
                ->icon('heroicon-o-calendar'),
            Stat::make(__('Pending Verifications'), $pendingRegistrations)
                ->description(__('Awaiting payment verification'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color($pendingRegistrations > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-clipboard-document-check'),
            Stat::make(__('Connected Users'), $totalUsers)
                ->description(__('Users affiliated with this space'))
                ->icon('heroicon-o-users'),
        ];
    }
}
