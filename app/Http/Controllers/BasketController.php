<?php

namespace App\Http\Controllers;

use App\Models\Basket;
use App\Models\BasketItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BasketController extends Controller
{
    public function products()
    {
        $products = Product::all();
        return response()->json(['data' => $products]);
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
}
