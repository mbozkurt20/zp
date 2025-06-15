<?php

namespace App\Http\Controllers;

use App\Filament\Resources\BasketResource;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ProductCollection;
use App\Models\Basket;
use App\Models\BasketItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BasketController extends Controller
{
    public function products()
    {
        $products = Product::all();
        return response()->json(['data' => new ProductCollection($products)]);
    }
    public function orders()
    {
        $orders = Order::whereDate('created_at',Carbon::today())->orderByDesc('is_ready')->get();
        return response()->json(['data' => new OrderCollection($orders)]);
    }

    public function categories()
    {
        $categories = Category::all();
        return response()->json(['data' => $categories]);
    }

    public function addProduct(Request $request)
    {
        $userId = Auth::id();

        $cart = json_decode($request->cart);

        if (!Basket::where('user_id', $userId)->where('is_completed', false)->where('is_shopping', true)->exists()) {
            $basket = Basket::create([
                'user_id' => $userId,
                'is_shopping' => true,
                'is_completed' => false,
            ]);
        } else {
            $basket = Basket::where('user_id', $userId)->where('is_completed', false)->where('is_shopping', true)->first();
        }

        $basket->cart = json_encode($cart);
        $basket->update();

        foreach ($cart as $item) {
            if ($item->quantity > Product::find($item->id)->quantity) {
                return response()->json(['message' => 'Ürün Stoğu yeterli değil'],400);
            }

          if (!BasketItem::where('basket_id', $basket->id)->where('product_id', $item->id)->exists()) {
              BasketItem::create([
                  'basket_id' => $basket->id,
                  'product_id' => $item->id,
                  'quantity' => $item->quantity,
              ]);
          }else{
              $basketItem = BasketItem::where('basket_id', $basket->id)->where('product_id', $item->id)->first();
              $basketItem->quantity = $item->quantity;
              $basketItem->update();
          }
        }
    }

    public function removeProduct(Request $request)
    {
        $userId = Auth::id();

        $cart = json_decode($request->cart);

        $basket = Basket::where('user_id', $userId)->where('is_completed', false)->where('is_shopping', true)->first();
        $basket->cart = json_encode($cart);
        $basket->update();

        if (isset($cart)){
            foreach ($cart as $item) {
                $basketItem = BasketItem::where('basket_id', $basket->id)->where('product_id', $item->id)->first();
                $basketItem->quantity = $item->quantity;
                $basketItem->update();
            }
        }
    }

    public function getCart()
    {
        $userId = Auth::id();
        $basket = Basket::where('user_id', $userId)->where('is_completed', false)->where('is_shopping', true)->first();

        return response()->json(['data' => $basket->cart]);
    }
}
