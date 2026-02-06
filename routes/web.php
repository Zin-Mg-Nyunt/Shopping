<?php

use App\Http\Controllers\AddToCartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductController::class, 'home'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('product.show');
Route::post('/cart/add', AddToCartController::class)->name('cart.add');

Route::middleware(['auth'])->group(function(){
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::get('user/dashboard', [DashboardController::class,'user'])->name('user.dashboard');
    Route::get('admin/dashboard', [DashboardController::class,'admin'])->middleware('can:access-admin')->name('admin.dashboard');
    Route::get('admin/products', [ProductController::class, 'adminProducts'])->middleware('can:access-admin')->name('admin.products');
});

require __DIR__.'/settings.php';
