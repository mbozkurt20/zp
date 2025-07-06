<?php

namespace App\Observers;

use App\Helpers\Pusher;
use App\Models\Basket;
use App\Models\Order;
use App\Events\NewModelCreated;
use App\Models\Product;
use App\Models\ProductVariant;
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

        if (!Order::where('basket_id', $basket->id)->exists() && $basket->basketItems->count() && $basket->is_completed ) {
            $groupedItems = $basket->basketItems->groupBy('product_id');

            $total = 0;

            foreach ($groupedItems as $productId => $items) {
                $product = Product::find($productId);

                $totalQuantityToSubtract = 0;

                foreach ($items as $item) {
                    $variant = ProductVariant::find($item->product_variant_id);

                    if ($variant->type == 'Kilogram') {
                        $calculateQuantity = $item->quantity * ($variant->quantity * 1000);
                    } else {
                        $calculateQuantity = $item->quantity * $variant->quantity;
                    }

                    $totalQuantityToSubtract += $calculateQuantity;

                    // Toplam fiyatı da burada biriktir
                    $total += $variant->price * $item->quantity;
                }

                // Ürünün stoğunu tek seferde güncelle

                \Illuminate\Support\Facades\DB::table('products')->where('id', $productId)->update([
                    'quantity' => $product->quantity - $totalQuantityToSubtract,
                ]);
            }

            try {
                $order = Order::create([
                    'creator_id' => auth()->id(),
                    'user_id' => $basket->user_id,
                    'basket_id' => $basket->id,
                    'total' => $total,
                    'is_paid' => true,
                    'discount' => 0,
                ]);

                $basket->is_shopping = false;
                $basket->update();

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
