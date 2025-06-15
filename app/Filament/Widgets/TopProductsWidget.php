<?php

namespace App\Filament\Widgets;

use App\Models\Basket;
use App\Models\Order;
use Filament\Widgets\ChartWidget;

class TopProductsWidget extends ChartWidget
{
    protected static ?string $heading = 'Haftalık Satın Alınmayan Sepet';

    protected function getData(): array
    {
        $startDate = now()->subDays(6)->startOfDay(); // Son 7 gün
        $endDate = now()->endOfDay();

        $baskets = Basket::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->where('is_shopping',true)
            ->where('is_completed',false)
            ->get()
            ->keyBy('date');

        // 7 güne göre boş günleri sıfırla göster
        $labels = [];
        $counts = [];

        for ($i = 0; $i < 7; $i++) {
            $date = now()->subDays(6 - $i)->toDateString();
            $labels[] = \Carbon\Carbon::parse($date)->translatedFormat('d M'); // Türkçe ay
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
