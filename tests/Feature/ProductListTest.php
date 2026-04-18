<?php

use App\Models\Product;

test('products list renders paginated inertia response', function () {
    Product::factory()->count(10)->create();

    $response = $this->get(route('products.list'));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Products/List')
        ->where('products.per_page', 4)
        ->where('products.total', 10)
        ->where('products.last_page', 3)
        ->has('products.data', 4)
        ->has('products.links'));
});

test('products list page loads when there are no products', function () {
    $response = $this->get(route('products.list'));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Products/List')
        ->where('products.total', 0)
        ->has('products.data', 0));
});
