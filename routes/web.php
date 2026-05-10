<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogSettingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Settings\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'list'])->name('products.list');
Route::get('/products/{product:slug}', [ProductController::class, 'detail'])->name('product.detail');
Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/destroy/{id}', [CartController::class, 'destroy'])->name('cart.destroy');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::get('/user/dashboard', [DashboardController::class, 'userDashboard'])->name('user.dashboard');
    Route::delete('/profile/photo/destroy', [ProfileController::class, 'profilePhotoDestroy'])->name('profile.photo.destroy');

    Route::post('/order/store', [OrderController::class, 'store'])->name('order.store');
    Route::get('/orders/list', [OrderController::class, 'userList'])->name('user.orders.list');
    Route::get('/admin/orders', [OrderController::class, 'adminList'])->name('admin.orders');
    Route::patch('/admin/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.status.update');

    Route::post('/wishlist/add/{product}', [ProductController::class, 'addWishlist'])->name('wishlist.add');
    Route::get('/wishlist', [ProductController::class, 'showWishlist'])->name('user.wishlist');
    Route::delete('/wishlist/remove/{product}', [ProductController::class, 'removeWishlist'])->name('user.wishlist.remove');

    Route::get('/shipping/address', [AddressController::class, 'index'])->name('user.shipping.address');
    Route::post('/shipping/address/store', [AddressController::class, 'store'])->name('user.address.store');
    Route::put('/shipping/address/update/{address}', [AddressController::class, 'update'])->name('user.address.update');
    Route::delete('/shipping/address/destroy/{address}', [AddressController::class, 'destroy'])->name('user.address.destroy');

    Route::get('/admin/products', [ProductController::class, 'adminList'])->name('admin.products');
    Route::delete('/admin/products', [ProductController::class, 'destroy'])->name('admin.products.destroy');
    Route::post('/admin/products', [ProductController::class, 'adminStore'])->name('admin.products.store');
    Route::put('/admin/products/{product}', [ProductController::class, 'adminUpdate'])->name('admin.products.update');
    Route::get('/admin/customers', [UserController::class, 'customerList'])->name('admin.customers');

    Route::get('/admin/catalog-settings', [CatalogSettingController::class, 'index'])->name('admin.catalog.settings');
    Route::post('/admin/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::put('/admin/categories/{category}', [CategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/admin/categories/{category}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');
    Route::post('/admin/brands', [BrandController::class, 'store'])->name('admin.brands.store');
    Route::put('/admin/brands/{brand}', [BrandController::class, 'update'])->name('admin.brands.update');
    Route::delete('/admin/brands/{brand}', [BrandController::class, 'destroy'])->name('admin.brands.destroy');
    Route::post('/admin/promos', [PromoController::class, 'store'])->name('admin.promos.store');
    Route::put('/admin/promos/{promo}', [PromoController::class, 'update'])->name('admin.promos.update');
    Route::delete('/admin/promos/{promo}', [PromoController::class, 'destroy'])->name('admin.promos.destroy');
});

require __DIR__.'/settings.php';
