<?php

namespace App\Observers;

use App\Helpers\Pusher;
use App\Models\Basket;
use App\Models\Order;
use App\Events\NewModelCreated;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BasketObserver
{
    /**
     * Handle the Basket "created" event.
     */
    public function created(Basket $basket): void
    {
        //
    }
    /**
     * Handle the Basket "updated" event.
     */
    public function updated(Basket $basket): void
    {
        $basket = Basket::find($basket->id);

        if (!Order::where('basket_id', $basket->id)->exists() && $basket->is_completed && $basket->basketItems->count()) {

            $total = $basket->basketItems->sum(function($item) {
              $product = Product::find($item->product_id);

                $product->update([
                    'quantity' => $product->quantity - $item->quantity
                ]);

              return $product->price * $item->quantity;
            });

            try {
                $order = Order::create([
                    'creator_id' => auth()->id(),
                    'user_id' => $basket->user_id,
                    'basket_id' => $basket->id,
                    'total' => $total,
                    'is_paid' => true,
                    'discount' => 0,
                ]);

                Pusher::trigger('cart-channel','clear-cart-'.auth()->id(), $order);
            } catch (\Exception $exception) {
                Log::error($exception->getMessage());
            }
        }
    }

    public function updating(Basket $basket){
    }

    /**
     * Handle the Basket "deleted" event.
     */
    public function deleted(Basket $basket): void
    {
        //
    }

    /**
     * Handle the Basket "restored" event.
     */
    public function restored(Basket $basket): void
    {
        //
    }

    /**
     * Handle the Basket "force deleted" event.
     */
    public function forceDeleted(Basket $basket): void
    {
        //
    }
}
