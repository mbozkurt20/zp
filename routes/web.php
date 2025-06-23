<?php

use App\Http\Controllers\ProfileController;
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
