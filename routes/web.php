<?php

use App\Http\Controllers\AddToCartController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Settings\NewPasswordController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OtpVerifyController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Settings\PasswordResetController;

Route::get('/', [ProductController::class, 'home'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('product.show');

Route::get('/forgot-password', [PasswordResetController::class, 'create'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'store'])->name('password.email');
Route::get('change-email', [PasswordResetController::class, 'changeEmail'])->name('password.change-email');
Route::post('verify-otp', [OtpVerifyController::class, 'verifyOtp'])->name('password.verify-otp');
Route::get('reset-password/{token}', [NewPasswordController::class, 'show'])->name('password.reset');
Route::post('reset-password', [NewPasswordController::class, 'reset'])->name('password.update');

Route::middleware(['auth'])->group(function () {
    Route::post('/cart/add', AddToCartController::class)->name('cart.add');
    Route::get('/cart', [CartController::class, 'show'])->name('cart.show');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::get('user/dashboard', [DashboardController::class, 'user'])->name('user.dashboard');
    Route::get('user/orders', [OrderController::class, 'userOrders'])->name('user.orders');
    Route::get('user/orders/{id}', [OrderController::class, 'userOrderDetail'])->name('user.orders.show');
    Route::get('user/wishlist', [ProductController::class, 'userWishlist'])->name('user.wishlist');
    Route::get('user/address/book', [CustomerController::class, 'userAddressBook'])->name('user.address.book');
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
