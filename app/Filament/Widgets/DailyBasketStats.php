<?php

namespace App\Filament\Widgets;

use App\Models\Basket;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class DailyBasketStats extends ChartWidget
{
    protected static ?string $heading = 'Günlük Sepet İstatistikleri';

    protected function getData(): array
    {
        $data = Basket::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereDate('created_at', '>=', now()->subDays(6)) // son 7 gün
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = [];
        $counts = [];

        foreach ($data as $day) {
            $labels[] = \Carbon\Carbon::parse($day->date)->translatedFormat('d M'); // Türkçe ay
            $counts[] = $day->count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Sepet Sayısı',
                    'data' => $counts,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line'; // veya 'bar'
    }
}
