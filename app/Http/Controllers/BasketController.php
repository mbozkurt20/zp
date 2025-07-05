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
use App\Models\ProductVariant;
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

    public function activeBasket(){
        $activeBasket = Basket::where('user_id',\Illuminate\Support\Facades\Auth::id())
            ->where('is_shopping',true)
            ->where('is_completed',false)
            ->select('id','cart')
            ->first();

        return response()->json(['cart' => $activeBasket ? json_decode($activeBasket->cart) : []]);
    }

    public function isCheckout($id){
        $basket = Basket::find($id);

        $basket->is_checkout = !$basket->is_checkout;
        $basket->update();


        if ($basket->is_checkout){
            return response()->json(['data' => $basket,'message' => "<strong>Siparişleriniz Hazırlanıyor,</strong> <br><br> <strong>Bizi Tercih Ettiğiniz için Teşekkürler!!</strong>"]);
        }else{
            return response()->json(['data' => $basket,'message' => '<strong>Keyifli Alışverişler Dileriz !!</strong> ']);
        }
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

    public function isProductStock(Request $request)
    {
        $userId = Auth::id();
        $cart = json_decode($request->cart);
        $productData = Product::find($request->productId);

        // Cart JSON'unu array olarak decode et
        $products = json_decode($request->cart, true); // artık array

// Eğer key'li associative array varsa, sadece value'ları al
        $products = array_values($products);

// Aynı ürün ID'ye sahip olanları filtrele
        $sameProductItems = array_filter($products, function($product) use ($productData) {
            return $product['id'] == $productData->id;
        });

// Toplam gram hesabı
        $totalGram = array_sum(array_map(function($product) {
            // Variant'ı bul (cart içinden gelen variantId ile)
            $variant = collect($product['variants'])->firstWhere('id', $product['variantId']);

            if (!$variant) {
                return 0; // variant yoksa 0 say
            }

            $variantQuantity = $variant['quantity'];

            if ($variant['type'] == 'Kilogram') {
                $variantQuantity = $variantQuantity * 1000;
            }

            return $product['quantity'] * $variantQuantity;
        }, $sameProductItems));

// Stok kontrolü
        if ($productData->quantity < $totalGram) {
            return response()->json([
                'message' => "Üzgünüz, {$productData->name} Stoğu Yetersiz"
            ], 400);
        }

        return response()->json(['status' => true]);
    }
    public function addProduct(Request $request)
    {
        $userId = Auth::id();
        $cart = json_decode($request->cart);
        $productData = Product::find($request->productId);
        $variantData = ProductVariant::find($request->variantId);

        $basket = Basket::where('user_id', $userId)
            ->where('is_completed', false)
            ->where('is_shopping', true)
            ->first();

        if (!$basket) {
            $basket = Basket::create([
                'user_id' => $userId,
                'is_shopping' => true,
                'is_completed' => false,
            ]);
        }

        $basket->cart = json_encode($cart);

        // Cart JSON'unu array olarak decode et
        $products = json_decode($request->cart, true); // artık array

// Eğer key'li associative array varsa, sadece value'ları al
        $products = array_values($products);

// Aynı ürün ID'ye sahip olanları filtrele
        $sameProductItems = array_filter($products, function($product) use ($productData) {
            return $product['id'] == $productData->id;
        });

// Toplam gram hesabı
        $totalGram = array_sum(array_map(function($product) {
            // Variant'ı bul (cart içinden gelen variantId ile)
            $variant = collect($product['variants'])->firstWhere('id', $product['variantId']);

            if (!$variant) {
                return 0; // variant yoksa 0 say
            }

            $variantQuantity = $variant['quantity'];

            if ($variant['type'] == 'Kilogram') {
                $variantQuantity = $variantQuantity * 1000;
            }

            return $product['quantity'] * $variantQuantity;
        }, $sameProductItems));

// Stok kontrolü
        if ($productData->quantity < $totalGram) {
            return response()->json([
                'message' => "Üzgünüz, {$productData->name} Stoğu Yetersiz"
            ], 400);
        }

        // Stok kontrolünden geçti, sepeti kaydet
         $basket->update();

        // BasketItem güncelleme / ekleme
        foreach ($cart as $item) {
            $basketItem = BasketItem::where('basket_id', $basket->id)
                ->where('product_id', $item->id)
                ->where('product_variant_id', $item->variantId)
                ->first();

            if ($basketItem) {
                $basketItem->quantity = $item->quantity;
                $basketItem->update();
            } else {
                BasketItem::create([
                    'basket_id' => $basket->id,
                    'product_id' => $item->id,
                    'product_variant_id' => $item->variantId,
                    'quantity' => $item->quantity,
                ]);
            }
        }

        return response()->json(['message' => 'Ürün Sepete Eklendi']);
    }

    public function clearCart()
    {
        $activeBasket = Basket::where('user_id',\Illuminate\Support\Facades\Auth::id())
            ->where('is_shopping',true)
            ->where('is_completed',false)
            ->select('id','cart')
            ->first();

        $activeBasket->cart = [];

        $activeBasket->update();

        return response()->json(['data' => $activeBasket,'message' => 'Sepetiniz Temizlendi']);
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
