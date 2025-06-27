<?php

use App\Http\Controllers\ProfileController;
use App\Models\Order;
use App\Models\Product;
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

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/basket', function () {
    return Inertia::render('Basket');
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
    Route::post('/remove-product', [\App\Http\Controllers\BasketController::class, 'removeProduct']);


    Route::post('/scan-product', function (Request $request) {
        $barcode = $request->barcode;
        $quantity = max(1, (int) $request->quantity);

        $product = Product::where('barcode', $barcode)->first();

        if (!$product) {
            return response()->json(['message' => 'Ürün Bulunamadı'], 404);
        }

        $cart = session('cart', []);
        $existingIndex = collect($cart)->search(fn($item) => $item['id'] === $product->id);

        if ($existingIndex !== false) {
            $cart[$existingIndex]['quantity'] += $quantity;
        } else {
            $cart[] = [
                'id' => $product->id,
                'tax' => $product->tax,
                'name' => $product->name,
                'image' => $product->image,
                'price' => $product->price,
                'barcode' => $product->barcode,
                'qr_code' => $product->qr_code,
                'discount' => $product->discount,
                'quantity' => $quantity,
                'created_at' => $product->discount,
                'stock_type' => $product->discount,
                'category_id' => $product->discount,
                'description' => $product->discount,
                'category_name' => $product->discount,
                'warning_quantity' => $product->warning_quantity,
            ];
        }

        session(['cart' => $cart]);
        return response()->json(['cart' => $cart]);
    });

    Route::post('/update-cart-quantity', function (Request $request) {
        $cart = session('cart', []);
        if (isset($cart[$request->index])) {
            $cart[$request->index]['quantity'] = max(1, (int) $request->quantity);
        }
        session(['cart' => $cart]);
        return response()->json(['cart' => $cart]);
    });

    Route::post('/remove-from-cart', function (Request $request) {
        $cart = session('cart', []);
        unset($cart[$request->index]);
        $cart = array_values($cart);
        session(['cart' => $cart]);
        return response()->json(['cart' => $cart]);
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
        ]);

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['quantity'] * $item['price'];

            \App\Models\BasketItem::create([
                'basket_id' => $basket->id,
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
            ]);
        }

        $barcode = 'o-' . rand(100000000, 999999999);

        if (Order::where('barcode', $barcode)->exists()) {
            $barcode = 'o-' . rand(100000000, 999999999);
        }

        \App\Models\Order::create([
            'creator_id' => auth()->id(),
            'user_id' => auth()->id(),
            'basket_id' => $basket->id,
            'barocde' => $barcode,
            'total' => $total,
            'is_paid' => 1,
            'is_ready' => 0,
        ]);

        session()->forget('cart');
        return response()->json(['message' => 'Satış Tamamlandı!']);
    });
});

Route::get('barcode/print/{order}',function (\App\Models\Order $order){
    return view('barcode.order-print', compact('order'));
})->name('barcode.order.print');

Route::get('receipt/print/{order}',function (\App\Models\Order $order){
    return view('barcode.receipt-order-print', compact('order'));
})->name('barcode.receipt.order.print');

Route::get('barcode/print/{product}',function (\App\Models\Product $product){
    return view('barcode.product-print', compact('product'));
})->name('barcode.print');

Route::get('barcode/bulk-print',function (Request $request){
    $ids = explode(',', $request->input('ids'));
    $products = Product::whereIn('id', $ids)->get();

    return view('barcode.product-bulk-print', compact('products'));
})->name('barcode.bulk.print');

require __DIR__.'/auth.php';
