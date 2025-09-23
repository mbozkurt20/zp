<?php

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
       'imagess' => \App\Models\Product::all()
    ]);
})->middleware(['auth', 'verified'])->name('welcome');

Route::post('/create-photo', function (Request $request) {
    // ✅ Hem görsel hem video için doğrulama
    $request->validate([
        'description' => 'nullable|string',
        'file' => 'required|mimes:jpg,jpeg,png,mp4,webm,webp', // 20 MB
    ]);

    // ✅ Dosya bilgileri
    $file = $request->file('file');
    $mime = $file->getMimeType();
    $type = str_starts_with($mime, 'video') ? 'video' : 'image';

    // ✅ Kayıt dizini
    $directory = $type === 'video' ? 'videos' : 'images';

    // ✅ Dosyayı public diskine kaydet
    $path = $file->store($directory, 'public');

    // ✅ Veritabanı kaydı
    $item = Product::create([
        'slug'        => Str::slug(Str::random(25)),
        'description' => $request->description ?? ' ',
        'image'       => $path,      // mevcut alan image adıyla kalabilir
        'type'        => $type,      // ✅ tabloya 'type' (image/video) kolonu ekleyin
    ]);

    return response()->json([
        'success' => true,
        'message' => ucfirst($type) . ' başarıyla yüklendi!',
        'data'    => $item,
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
