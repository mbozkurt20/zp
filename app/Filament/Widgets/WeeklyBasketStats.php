<?php

namespace App\Filament\Widgets;

use App\Models\Basket;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class WeeklyBasketStats extends ChartWidget
{
    protected static ?string $heading = 'Haftalık Sepet İstatistikleri';

    protected function getData(): array
    {
        $startDate = now()->subDays(6)->startOfDay(); // Son 7 gün
        $endDate = now()->endOfDay();

        $baskets = Basket::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // 7 güne göre boş günleri sıfırla göster
        $labels = [];
        $counts = [];

        for ($i = 0; $i < 7; $i++) {
            $date = now()->subDays(6 - $i)->toDateString();
            $labels[] = Carbon::parse($date)->translatedFormat('d M'); // Türkçe ay
            $counts[] = $baskets[$date]->count ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Sepet Sayısı',
                    'data' => $counts,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
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
