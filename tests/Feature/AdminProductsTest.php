<?php

use App\Models\User;

test('guests cannot visit admin products', function () {
    $response = $this->get(route('admin.products'));

    $response->assertRedirect(route('login'));
});

test('authenticated users can visit admin products with inventory data', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('admin.products'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Admin/Products')
        ->has('products'));
});
