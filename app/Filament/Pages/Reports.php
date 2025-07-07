<?php

namespace App\Filament\Pages;

use App\Models\Basket;
use App\Models\BasketItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
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

// Ürünler ve varyantlar
        $products = Product::with('variants')->get();

// Rapor verisi: ürün eklenme ve satış sayısı
        $reportItems = $products->map(function ($product) {
            $addedCount = BasketItem::where('product_id', $product->id)->count();
            $soldCount = DB::table('basket_items')
                ->where('product_id', $product->id)
                ->sum('quantity');

            $soldTotal = BasketItem::where('product_id', $product->id)
                ->with('productVariant') // ilişkili variant'ları da al
                ->get()
                ->sum(function ($basketItem) {
                    // Varsayım: her basket item sadece bir variant ile ilişkili
                    $variant = $basketItem->productVariant->first(); // Eğer ilişkisi birden fazlaysa burayı değiştirmen gerekebilir
                    return $variant ? $variant->price * $basketItem->quantity : 0;
                });

            return [
                'product' => $product,
                'added_count' => $addedCount,
                'sold_count' => $soldCount,
                'soldTotal' => $soldTotal,
            ];
        });

        $this->data['reportItems'] = $reportItems;

// ============================
// 💰 KAR ZARAR HESAPLAMALARI
// ============================

// 1. Gerçekleşen satışlardan kar/zarar
        $completedBaskets = Basket::where('is_shopping', false)
            ->where('is_completed', true)
            ->with(['basketItems']) // performans için eager load
            ->get();

        $totalRealizedProfit = 0;

        foreach ($completedBaskets as $basket) {
            foreach ($basket->basketItems as $item) {
                $variant = $item->productVariant;
                $product = $variant?->product;

                if (!$variant || !$product) {
                    continue;
                }

                $purchasePrice = $product->purchase_price; // alış fiyatı
                $salePrice = $variant->price;              // satış fiyatı
                $quantity = $item->quantity;

                $profit = ($salePrice - $purchasePrice) * $quantity;
                $totalRealizedProfit += $profit;
            }
        }

// 2. Tüm stoklar satılsaydı oluşacak kar
        $variants = ProductVariant::with('product')->get();

        $totalPotentialProfit = 0;

        foreach ($variants as $variant) {
            $product = $variant->product;

            if (!$product) {
                continue;
            }

            $stock = $product->quantity;
            $purchasePrice = $product->purchase_price;
            $salePrice = $variant->price;

            $potentialProfit = ($salePrice - $purchasePrice) * $stock;
            $totalPotentialProfit += $potentialProfit;
        }

        $this->data['realizedProfit'] = $totalRealizedProfit;
        $this->data['potentialProfit'] = $totalPotentialProfit;

    }
}
