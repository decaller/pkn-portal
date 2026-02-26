<?php

namespace App\Filament\Widgets;

use App\Enums\RegistrationStatus;
use App\Models\EventRegistration;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;

class RevenueChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    public function getHeading(): string
    {
        return 'Monthly Revenue (Paid Registrations)';
    }

    protected function getData(): array
    {
        $tenant = Filament::getTenant();
        $now = now();
        $data = [];
        $labels = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $labels[] = $month->format('M Y');

            $revenue = EventRegistration::where('organization_id', $tenant?->id)
                ->where('status', RegistrationStatus::Paid)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('total_amount');

            $data[] = $revenue;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (IDR)',
                    'data' => $data,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
