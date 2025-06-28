<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Product;

class ProductObserver
{
    public function creating(Product $product)
    {
       /* $barcode = 'p-' . rand(100000000, 999999999);

        if (Product::where('barcode', $barcode)->exists()) {
            $barcode = 'p-' . rand(100000000, 999999999);
        }

        $product->barcode = $barcode; */
    }

    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
     //
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        //
    }
}
