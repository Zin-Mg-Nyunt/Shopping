<?php

use App\Models\Product;
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

test('guests cannot delete a product', function () {
    $product = Product::factory()->create();

    $this->delete(route('admin.products.destroy'), ['ids' => [$product->id]])
        ->assertRedirect(route('login'));
});

test('non-admin users cannot delete a product', function () {
    $user = User::factory()->create(['role' => 'user']);
    $product = Product::factory()->create();

    $this->actingAs($user)
        ->delete(route('admin.products.destroy'), ['ids' => [$product->id]])
        ->assertForbidden();
});

test('admin can delete a product', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $product = Product::factory()->create();

    $this->actingAs($admin)
        ->delete(route('admin.products.destroy'), ['ids' => [$product->id]])
        ->assertRedirect();

    $this->assertSoftDeleted($product);
});

test('admin can bulk delete products', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $first = Product::factory()->create();
    $second = Product::factory()->create();

    $this->actingAs($admin)
        ->delete(route('admin.products.destroy'), [
            'ids' => [$first->id, $second->id],
        ])
        ->assertRedirect();

    $this->assertSoftDeleted($first);
    $this->assertSoftDeleted($second);
});
