<?php

test('cart page renders with items', function () {
    $response = $this->get(route('cart'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Products/Cart')
        ->has('items'));
});

test('cart page can render empty cart', function () {
    $response = $this->get(route('cart', ['empty' => true]));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Products/Cart')
        ->where('items', []));
});
