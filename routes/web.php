<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return inertia('Welcome');
})->name('home');

Route::get('/products', function () {
    return inertia('Products/List');
})->name('products.list');

Route::get('/products/{id}', function ($id) {
    return inertia('Products/Detail', [
        'product' => [
            'id' => $id,
            'name' => 'Product '.$id,
            'description' => 'Description for product '.$id,
            'price' => 100 * $id,
        ],
    ]);
})->name('product.detail');

Route::get('/cart', function (Request $request) {
    $items = $request->boolean('empty')
        ? []
        : [
            [
                'id' => 1,
                'name' => 'Aero Running Shoes',
                'category' => 'Footwear · Running',
                'image_url' => null,
                'unit_price' => 129.0,
                'quantity' => 2,
            ],
            [
                'id' => 2,
                'name' => 'Classic Leather Backpack',
                'category' => 'Bags · Accessories',
                'image_url' => null,
                'unit_price' => 179.0,
                'quantity' => 1,
            ],
            [
                'id' => 3,
                'name' => 'Smart Fitness Watch',
                'category' => 'Electronics · Wearables',
                'image_url' => null,
                'unit_price' => 249.0,
                'quantity' => 1,
            ],
        ];

    return inertia('Products/Cart', [
        'items' => $items,
    ]);
})->name('cart');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
