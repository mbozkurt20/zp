<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

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
        Log::info('Girdi 1');
        if ($product->stock_type== 'Kilogram'){
            $product->quantity = $product->quantity * 1000;
            $product->warning_quantity = $product->warning_quantity * 1000;
            $product->save();
        }
    }


    /**
     * Handle the Product "updated" event.
     */
    public function updating(Product $product): void
    {
        if ($product->stock_type == 'Kilogram'){
            $product->quantity = (int)$product->quantity * 1000;
            $product->warning_quantity = (int)$product->warning_quantity * 1000;
        }
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
