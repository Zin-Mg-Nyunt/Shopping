<?php

use App\Http\Controllers\AddToCartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductController::class, 'home'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('product.show');
Route::post('/cart/add', AddToCartController::class)->name('cart.add');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::get('user/dashboard', [DashboardController::class, 'user'])->name('user.dashboard');
    Route::middleware(['can:access-staff-management'])->group(function () {
        Route::get('admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
        Route::get('admin/staff', [StaffController::class, 'staffManagement'])->name('admin.staff');
        Route::get('admin/products', [ProductController::class, 'adminProducts'])->name('admin.products');
        Route::delete('admin/products/{product:slug}/delete', [ProductController::class, 'adminDeleteProduct'])->name('admin.products.delete');
        Route::get('admin/products/create', [ProductController::class, 'adminCreateProduct'])->name('admin.products.create');
        Route::get('admin/products/{product:slug}/edit', [ProductController::class, 'adminCreateProduct'])->name('admin.products.edit');
        Route::post('admin/product/store', [ProductController::class, 'adminStoreProduct'])->name('admin.product.store');
        Route::put('admin/products/{product}/update', [ProductController::class, 'adminUpdateProduct'])->name('admin.product.update');
        Route::get('admin/orders', [OrderController::class, 'adminOrders'])->name('admin.orders');
        Route::get('admin/orders/{id}', [OrderController::class, 'show'])->name('admin.orders.show');
        Route::get('admin/customers', [CustomerController::class, 'index'])->name('admin.customers');
        Route::get('admin/categories', [CategoryController::class, 'index'])->name('admin.categories');
        Route::get('admin/coupons', [CouponController::class, 'index'])->name('admin.coupons');
        Route::get('admin/settings', [SettingController::class, 'index'])->name('website.setting');
    });
});

require __DIR__.'/settings.php';
