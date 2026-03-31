<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return inertia('Welcome');
})->name('home');
Route::get('/products', function(){
    return inertia('Products/List');
})->name('products.list');
Route::get('/products/{id}', function($id){
    return inertia('Products/Detail', [
        'product' => [
            'id' => $id,
            'name' => 'Product ' . $id,
            'description' => 'Description for product ' . $id,
            'price' => 100 * $id,
        ],
    ]);
})->name('product.detail');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
