<?php

namespace App\Filament\Pages;

use App\Models\Basket;
use App\Models\BasketItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Livewire\WithPagination;

class Reports extends Page
{
    use WithPagination;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static string $view = 'filament.pages.reports';
    protected static ?string $title = 'Raporlar';

    public $data = [];
    public $search = '';
    public $dateFrom;
    public $dateTo;

    public function mount(): void
    {
        $this->dateFrom = Carbon::now()->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
        $this->generateReport();
    }

    public function updated($property)
    {
        if (in_array($property, ['search', 'dateFrom', 'dateTo'])) {
            $this->generateReport();
        }
    }

    public function generateReport()
    {
        $start = Carbon::parse($this->dateFrom)->startOfDay();
        $end = Carbon::parse($this->dateTo)->endOfDay();

        $completedOrders = Order::whereBetween('created_at', [$start, $end])->get();
        $this->data['totalOrders'] = $completedOrders->count();
        $this->data['totalRevenue'] = $completedOrders->sum('total');

        // Ödeme Tipi Bazlı Rapor
        $paymentTypes = ['Kredi Kart', 'Nakit', 'Eft/Havale'];
        $paymentTypeReport = [];

        foreach ($paymentTypes as $type) {
            $baskets = Basket::where('payment_type', $type)
                ->where('is_completed', true)
                ->whereBetween('created_at', [$start, $end])
                ->with(['basketItems.productVariant'])
                ->get();

            $totalRevenue = 0;

            foreach ($baskets as $basket) {
                foreach ($basket->basketItems as $item) {
                    $variant = $item->productVariant;
                    if ($variant) {
                        $totalRevenue += $variant->price * $item->quantity;
                    }
                }
            }

            $paymentTypeReport[] = [
                'payment_type' => $type,
                'basket_count' => $baskets->count(),
                'total_revenue' => $totalRevenue,
            ];
        }

        $this->data['paymentTypeReport'] = $paymentTypeReport;

        // Ürün Bazlı Rapor
        $products = Product::with('variants')
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->get();

        $reportItems = $products->map(function ($product) use ($start, $end) {
            $addedCount = BasketItem::where('product_id', $product->id)
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $soldCount = BasketItem::where('product_id', $product->id)
                ->whereBetween('created_at', [$start, $end])
                ->sum('quantity');

            $soldTotal = BasketItem::where('product_id', $product->id)
                ->whereBetween('created_at', [$start, $end])
                ->with('productVariant')
                ->get()
                ->sum(function ($item) {
                    $variant = $item->productVariant?->first();
                    return $variant ? $variant->price * $item->quantity : 0;
                });

            return [
                'product' => $product,
                'added_count' => $addedCount,
                'sold_count' => $soldCount,
                'soldTotal' => $soldTotal,
            ];
        });

        $this->data['reportItems'] = $reportItems;

        // Kar Hesaplaması
        $completedBaskets = Basket::where('is_shopping', false)
            ->where('is_completed', true)
            ->whereBetween('created_at', [$start, $end])
            ->with(['basketItems'])
            ->get();

        $totalRealizedProfit = 0;

        foreach ($completedBaskets as $basket) {
            foreach ($basket->basketItems as $item) {
                $variant = $item->productVariant;
                $product = $variant?->product;

                if (!$variant || !$product) continue;

                $profit = ($variant->price - $product->purchase_price) * $item->quantity;
                $totalRealizedProfit += $profit;
            }
        }

        $this->data['realizedProfit'] = $totalRealizedProfit;
    }
}
