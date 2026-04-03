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
    Route::inertia('admin/dashboard', 'Admin/Dashboard')->name('admin.dashboard');
    Route::get('/admin/products', function () {
        return inertia('Admin/Products', [
            'products' => [
                [
                    'id' => 1,
                    'name' => 'Aero Running Shoes',
                    'sku' => 'SNK-AERO-01',
                    'category' => 'Footwear',
                    'price' => 129.0,
                    'stock' => 142,
                    'stock_max' => 200,
                    'status' => 'published',
                    'image_url' => null,
                ],
                [
                    'id' => 2,
                    'name' => 'Classic Leather Backpack',
                    'sku' => 'BAG-CLSC-09',
                    'category' => 'Bags',
                    'price' => 179.0,
                    'stock' => 8,
                    'stock_max' => 120,
                    'status' => 'published',
                    'image_url' => null,
                ],
                [
                    'id' => 3,
                    'name' => 'Smart Fitness Watch',
                    'sku' => 'EL-WCH-22',
                    'category' => 'Electronics',
                    'price' => 249.0,
                    'stock' => 0,
                    'stock_max' => 80,
                    'status' => 'out_of_stock',
                    'image_url' => null,
                ],
                [
                    'id' => 4,
                    'name' => 'Noise-Canceling Headphones',
                    'sku' => 'AUD-NC-300',
                    'category' => 'Electronics',
                    'price' => 299.0,
                    'stock' => 64,
                    'stock_max' => 100,
                    'status' => 'published',
                    'image_url' => null,
                ],
                [
                    'id' => 5,
                    'name' => 'Portable Espresso Maker',
                    'sku' => 'KIT-ESP-02',
                    'category' => 'Home',
                    'price' => 89.0,
                    'stock' => 3,
                    'stock_max' => 60,
                    'status' => 'draft',
                    'image_url' => null,
                ],
                [
                    'id' => 6,
                    'name' => 'Organic Cotton Hoodie',
                    'sku' => 'APP-HOOD-44',
                    'category' => 'Apparel',
                    'price' => 74.0,
                    'stock' => 210,
                    'stock_max' => 250,
                    'status' => 'published',
                    'image_url' => null,
                ],
                [
                    'id' => 7,
                    'name' => 'Desk Organizer Set',
                    'sku' => 'OFF-DESK-11',
                    'category' => 'Home',
                    'price' => 59.0,
                    'stock' => 5,
                    'stock_max' => 90,
                    'status' => 'published',
                    'image_url' => null,
                ],
                [
                    'id' => 8,
                    'name' => 'Ceramic Water Bottle',
                    'sku' => 'KIT-BTL-08',
                    'category' => 'Home',
                    'price' => 45.0,
                    'stock' => 88,
                    'stock_max' => 150,
                    'status' => 'draft',
                    'image_url' => null,
                ],
                [
                    'id' => 9,
                    'name' => 'Trail Running Jacket',
                    'sku' => 'APP-JKT-19',
                    'category' => 'Apparel',
                    'price' => 159.0,
                    'stock' => 12,
                    'stock_max' => 80,
                    'status' => 'published',
                    'image_url' => null,
                ],
                [
                    'id' => 10,
                    'name' => 'USB-C Hub Pro',
                    'sku' => 'EL-HUB-07',
                    'category' => 'Electronics',
                    'price' => 49.0,
                    'stock' => 0,
                    'stock_max' => 200,
                    'status' => 'out_of_stock',
                    'image_url' => null,
                ],
                [
                    'id' => 11,
                    'name' => 'Yoga Mat Premium',
                    'sku' => 'FIT-MAT-03',
                    'category' => 'Fitness',
                    'price' => 68.0,
                    'stock' => 7,
                    'stock_max' => 100,
                    'status' => 'published',
                    'image_url' => null,
                ],
                [
                    'id' => 12,
                    'name' => 'Stainless Travel Mug',
                    'sku' => 'KIT-MUG-15',
                    'category' => 'Home',
                    'price' => 32.0,
                    'stock' => 400,
                    'stock_max' => 500,
                    'status' => 'published',
                    'image_url' => null,
                ],
            ],
        ]);
    })->name('admin.products');
});

require __DIR__.'/settings.php';
