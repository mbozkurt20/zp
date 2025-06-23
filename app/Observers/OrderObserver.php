<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Product;

class OrderObserver
{
    public function creating(Order $order)
    {
        $barcode = 'o-' . rand(100000000, 999999999);

        if (Order::where('barcode', $barcode)->exists()) {
            $barcode = 'o-' . rand(100000000, 999999999);
        }

        $order->barcode = $barcode;
    }
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
