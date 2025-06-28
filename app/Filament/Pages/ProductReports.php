<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;

class ProductReports extends Page
{

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static string $view = 'filament.pages.product-reports';
    protected static ?string $navigationLabel = 'Ürün Raporları';
    protected static ?string $title = 'Ürün Raporları';

    public $products;
    public $totalPurchase;
    public $totalSales;
    public $warningCount;
    public $totalCount;

    public function mount(): void
    {
        $this->products = Product::all();

        $this->totalPurchase = $this->products->sum(function ($product) {
            return $product->purchase_price * $product->quantity;
        });

        $this->totalSales = $this->products->sum(function ($product) {
            return $product->price * $product->quantity;
        });

        $this->warningCount = $this->products->filter(function ($product) {
            return $product->warning_quantity !== null && $product->quantity < $product->warning_quantity;
        })->count();

        $this->totalCount = $this->products->count();
    }
}
