<?php

namespace App\Filament\Pages;

use App\Models\BasketItem;
use App\Models\Order;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class Reports extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static string $view = 'filament.pages.reports';
    protected static ?string $title = 'Raporlar';

    public $data = [];
    public function mount(): void
    {
        $this->data['totalOrders'] = Order::count();
        $this->data['totalRevenue'] = Order::sum('total');

        $products = \App\Models\Product::all();


        $reportItems = $products->map(function ($product) {
            $addedCount = \App\Models\BasketItem::where('product_id', $product->id)->count();
            $soldCount = DB::table('basket_items')
                ->where('product_id', $product->id)
                ->sum('quantity');

            return [
                'product' => $product,
                'added_count' => $addedCount,
                'sold_count' => $soldCount,
            ];
        });

        $this->data['reportItems'] = $reportItems;
    }
}
