<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class DailyOrderStats extends ChartWidget
{
    protected static ?string $heading = 'Günlük Kazanç';

    protected function getData(): array
    {
        $dailyEarnings = DB::table('orders')
            ->selectRaw('DATE(created_at) as date, SUM(total) as earnings')
            ->whereDate('created_at', '>=', now()->subDays(7)) // Son 7 gün
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Günlük Kazanç ₺',
                    'data' => $dailyEarnings->pluck('earnings')->toArray(),
                ],
            ],
            'labels' => $dailyEarnings->pluck('date')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
