<?php

namespace App\Providers;

use App\Models\Basket;
use App\Models\Order;
use App\Models\Product;
use App\Observers\BasketObserver;
use App\Observers\OrderObserver;
use App\Observers\ProductObserver;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        Basket::observe(BasketObserver::class);
        Product::observe(ProductObserver::class);
        Order::observe(OrderObserver::class);

    }
}
