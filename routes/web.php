<?php

use App\Http\Controllers\ProfileController;
use App\Models\Basket;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Picqer\Barcode\BarcodeGeneratorPNG;

Route::get('/', function () {
    return Inertia::render('Welcome', [
       'imagess' => \App\Models\Product::all()
    ]);
})->middleware(['auth', 'verified'])->name('welcome');

Route::post('/create-photo', function (Request $request) {
    $request->validate([
        'description' => 'nullable|string',
        'file' => 'nullable|image|max:10240', // 10MB
    ]);

    // Görsel yükleme
    $imagePath = $request->file('file')
        ? $request->file('file')->store('images', 'public')
        : null;

    // Yeni kayıt
    $song = Product::create([
        'slug' =>  Str::slug(Str::random(25)),
        'description' => $request->description,
        'image' => $imagePath,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Görsel Başarıyla Eklendi!',
        'data' => $song,
    ]);
})->middleware(['auth', 'verified']);

Route::get('/add', function () {
    return Inertia::render('Add', [
        'categories' => \App\Models\Category::all()
    ]);
})->middleware(['auth', 'verified'])->name('add');

Route::get('/categories-all', function () {
    return Inertia::render('Categories', [
        'categories' => \App\Models\Category::all()
    ]);
})->name('categories.all');

Route::get('/day', function () {
    return Inertia::render('Day', [
        'categories' => \App\Models\Category::all()
    ]);
})->name('day');

Route::get('songs/liked/{id}', function ($id) {
    $product = Product::find($id);

    $product->liked++;
    $product->update();
    return 'OK';
});

Route::post('songs/remove/{id}', function ($id) {
    $product = Product::find($id);
    $product->delete();

    return response()->json(['message' => 'Günün Şarkısı Silindi']);
});

Route::post('/songs/update-image/{id}', function (Request $request,$id) {
    $song = Product::findOrFail($id);

    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('songs', 'public');
        $song->image = $path;
        $song->save();
    }

    return response()->json(['message' => 'Image updated']);
});

Route::get('/orders', function () {
    return Inertia::render('Orders');
})->middleware(['auth', 'verified'])->name('orders');

Route::get('/products', [\App\Http\Controllers\BasketController::class, 'products']);

Route::middleware('auth')->group(function () {

});


require __DIR__.'/auth.php';
