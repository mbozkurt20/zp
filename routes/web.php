<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

require __DIR__.'/auth.php';
