<?php

use App\Http\Controllers\ProfileController;
use App\Models\Basket;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Picqer\Barcode\BarcodeGeneratorPNG;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');

Route::get('/category/{id}', function ($id) {
    $products = Product::where('category_id', $id)->with('variants')->get();

    return Inertia::render('Products', [
        'products' => $products,
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'isLogin' => auth()->check(),
        'phpVersion' => PHP_VERSION,
    ]);
})->name('products');

Route::get('/dashboard', function () {
    $products = Product::with('variants')->get();

    return Inertia::render('Dashboard',[
        'products' => $products,
    ]);

})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/basket', function () {
    $activeBasket = Basket::where('user_id',\Illuminate\Support\Facades\Auth::id())
        ->where('is_shopping',true)
        ->where('is_completed',false)
        ->select('id','is_checkout')
        ->first();

    return Inertia::render('Basket',[
        'activeBasket' => $activeBasket,
    ]);
})->middleware(['auth', 'verified'])->name('basket');

Route::get('/orders', function () {
    return Inertia::render('Orders');
})->middleware(['auth', 'verified'])->name('orders');

Route::get('/products', [\App\Http\Controllers\BasketController::class, 'products']);
Route::get('/categories', [\App\Http\Controllers\BasketController::class, 'categories']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/order', [\App\Http\Controllers\BasketController::class, 'orders']);
    Route::get('/cart', [\App\Http\Controllers\BasketController::class, 'getCart']);
    Route::post('/add-product', [\App\Http\Controllers\BasketController::class, 'addProduct']);
    Route::post('/is-product', [\App\Http\Controllers\BasketController::class, 'isProductStock']);
    Route::post('/remove-product', [\App\Http\Controllers\BasketController::class, 'removeProduct']);
    Route::post('/clear-cart', [\App\Http\Controllers\BasketController::class, 'clearCart']);

    Route::get('/checkout/basket/{id}', [\App\Http\Controllers\BasketController::class, 'isCheckout']);
    Route::get('/active/basket/', [\App\Http\Controllers\BasketController::class, 'activeBasket']);

    Route::get('/scan-product', function (Request $request) {
        $barcode = $request->barcode;

        $product = Product::where('barcode', $barcode)->with('variants')->first();

        if (!$product) {
            return response()->json(['message' => 'Ürün Bulunamadı'], 404);
        }

        return response()->json([
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'image' => $product->image,
                'variants' => $product->variants->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'type' => $variant->type,
                        'price' => $variant->price,
                        'quantity' => $variant->quantity,
                    ];
                })
            ]
        ]);
    });

    Route::post('/checkout', function (Request $request) {
        $cart = $request->cart;
        $payment = $request->payment;

        $basket = \App\Models\Basket::create([
            'user_id' => auth()->id(),
            'is_shopping' => 0,
            'is_completed' => 1,
            'cart' => json_encode($cart),
            'payment_type' => $payment,
            'is_checkout' => 1,
        ]);

        foreach ($cart as $item) {
            \App\Models\BasketItem::create([
                'basket_id' => $basket->id,
                'product_id' => $item['id'],
                'product_variant_id' => $item['variantId'],
                'quantity' => $item['quantity'],
            ]);
        }

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

        $barcode = 'o-' . rand(100000000, 999999999);

        if (Order::where('barcode', $barcode)->exists()) {
            $barcode = 'o-' . rand(100000000, 999999999);
        }

        $order = \App\Models\Order::create([
            'creator_id' => auth()->id(),
            'user_id' => auth()->id(),
            'basket_id' => $basket->id,
            'barcode' => $barcode,
            'total' => $total,
            'is_paid' => 1,
            'is_ready' => 0,
        ]);

        session()->forget('cart');
        return response()->json(['order' => $order, 'message' => 'Satış Tamamlandı!']);
    });
});

Route::get('barcode/print/product/{product}',function (\App\Models\Product $product){
    return view('barcode.product-print', compact('product'));
})->name('barcode.print');

Route::get('barcode/print/order/{order}',function (\App\Models\Order $order){
    return view('barcode.order-print', compact('order'));
})->name('barcode.order.print');

Route::get('receipt/print/{order}',function (\App\Models\Order $order){
    return view('barcode.receipt-order-print', compact('order'));
})->name('barcode.receipt.order.print');


Route::get('barcode/bulk-print',function (Request $request){
    $ids = explode(',', $request->input('ids'));
    $products = Product::whereIn('id', $ids)->get();

    return view('barcode.product-bulk-print', compact('products'));
})->name('barcode.bulk.print');

require __DIR__.'/auth.php';
