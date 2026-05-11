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
use Illuminate\Support\Facades\Route;

Route::controller(ProductController::class)->group(function (){
    Route::get('/', 'index')->name('home');
    Route::get('/products', 'list')->name('products.list');
    Route::get('/products/{product:slug}', 'detail')->name('product.detail');
});

Route::controller(CartController::class)->group(function (){
    Route::get('/cart', 'index')->name('cart');
    Route::post('/cart/add', 'addToCart')->name('cart.add');
    Route::post('/cart/update', 'update')->name('cart.update');
    Route::delete('/cart/destroy/{id}', 'destroy')->name('cart.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    

    Route::middleware(['can:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');
        Route::get('/customers', [UserController::class, 'customerList'])->name('customers');
        Route::get('/catalog-settings', [CatalogSettingController::class, 'index'])->name('catalog.settings');

        Route::controller(OrderController::class)->group(function (){
            Route::get('/orders', 'adminList')->name('orders');
            Route::patch('/orders/{order}/status', 'updateStatus')->name('orders.status.update');
        });
        
        Route::controller(ProductController::class)->group(function (){
            Route::get('/products', 'adminList')->name('products');
            Route::delete('/products', 'destroy')->name('products.destroy');
            Route::post('/products', 'adminStore')->name('products.store');
            Route::put('/products/{product}', 'adminUpdate')->name('products.update');
        });

        Route::controller(CategoryController::class)->group(function (){
            Route::post('/categories', 'store')->name('categories.store');
            Route::put('/categories/{category}', 'update')->name('categories.update');
            Route::delete('/categories/{category}', 'destroy')->name('categories.destroy');
        });

        Route::controller(BrandController::class)->group(function (){
            Route::post('/brands', 'store')->name('brands.store');
            Route::put('/brands/{brand}', 'update')->name('brands.update');
            Route::delete('/brands/{brand}', 'destroy')->name('brands.destroy');
        });

        Route::controller(PromoController::class)->group(function (){
            Route::post('/promos', 'store')->name('promos.store');
            Route::put('/promos/{promo}', 'update')->name('promos.update');
            Route::delete('/promos/{promo}', 'destroy')->name('promos.destroy');
        }); 
    });
   
    Route::middleware(['can:user'])->prefix('user')->name('user.')->group(function (){
        Route::get('/dashboard', [DashboardController::class, 'userDashboard'])->name('dashboard');
    
        Route::controller(OrderController::class)->group(function (){
            Route::post('/orders/store', 'store')->name('order.store');
            Route::get('/orders/list', 'userList')->name('orders.list');
        });
    
        Route::controller(ProductController::class)->group(function (){
            Route::get('/wishlist', 'showWishlist')->name('wishlist');
            Route::post('/wishlist/add/{product}', 'addWishlist')->name('wishlist.add');
            Route::delete('/wishlist/remove/{product}', 'removeWishlist')->name('wishlist.remove');
        });
        
        Route::controller(AddressController::class)->group(function (){
            Route::get('/shipping/address', 'index')->name('shipping.address');
            Route::post('/shipping/address/store', 'store')->name('address.store');
            Route::put('/shipping/address/update/{address}', 'update')->name('address.update');
            Route::delete('/shipping/address/destroy/{address}', 'destroy')->name('address.destroy');
        });
    });
});

require __DIR__.'/settings.php';
