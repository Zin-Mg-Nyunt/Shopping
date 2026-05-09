<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Promo;
use App\Models\User;

test('guests cannot visit admin catalog settings', function () {
    $response = $this->get(route('admin.catalog.settings'));

    $response->assertRedirect(route('login'));
});

test('authenticated users can visit admin catalog settings', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('admin.catalog.settings'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Admin/CatalogSettings')
        ->has('categories')
        ->has('brands')
        ->has('promos'));
});

test('catalog category store validates required fields', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->from(route('admin.catalog.settings'))
        ->post(route('admin.categories.store'), [])
        ->assertSessionHasErrors(['name', 'slug']);
});

test('catalog category store redirects when valid', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->from(route('admin.catalog.settings'))
        ->post(route('admin.categories.store'), [
            'name' => 'New category',
            'slug' => 'new-category',
        ])
        ->assertRedirect(route('admin.catalog.settings'));
});

test('catalog category update redirects when valid', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $category = Category::query()->create([
        'name' => 'Original',
        'slug' => 'original-'.uniqid(),
    ]);

    $this->from(route('admin.catalog.settings'))
        ->put(route('admin.categories.update', $category), [
            'name' => 'Updated',
            'slug' => $category->slug,
        ])
        ->assertRedirect(route('admin.catalog.settings'));
});

test('catalog category destroy redirects', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $category = Category::query()->create([
        'name' => 'To remove',
        'slug' => 'to-remove-'.uniqid(),
    ]);

    $this->from(route('admin.catalog.settings'))
        ->delete(route('admin.categories.destroy', $category))
        ->assertRedirect(route('admin.catalog.settings'));
});

test('catalog promo store validates required fields', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->from(route('admin.catalog.settings'))
        ->post(route('admin.promos.store'), [])
        ->assertSessionHasErrors(['code', 'discount', 'discount_type']);
});

test('catalog promo store redirects when valid', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->from(route('admin.catalog.settings'))
        ->post(route('admin.promos.store'), [
            'code' => 'SAVE10',
            'discount' => 10,
            'discount_type' => 'percentage',
            'expires_at' => null,
            'user_limit' => null,
        ])
        ->assertRedirect(route('admin.catalog.settings'));
});

test('catalog promo store accepts decimal percentage discount', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->from(route('admin.catalog.settings'))
        ->post(route('admin.promos.store'), [
            'code' => 'SAVE125',
            'discount' => 12.5,
            'discount_type' => 'percentage',
            'expires_at' => null,
            'user_limit' => null,
        ])
        ->assertRedirect(route('admin.catalog.settings'));

    expect((float) Promo::query()->where('code', 'SAVE125')->value('discount'))
        ->toBe(12.5);
});

test('catalog promo store rejects percentage discount over 100', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->from(route('admin.catalog.settings'))
        ->post(route('admin.promos.store'), [
            'code' => 'TOOBIG',
            'discount' => 100.01,
            'discount_type' => 'percentage',
            'expires_at' => null,
            'user_limit' => null,
        ])
        ->assertSessionHasErrors(['discount']);
});

test('catalog promo update redirects when valid', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $promo = Promo::query()->create([
        'code' => 'OLD',
        'discount_type' => 'percentage',
        'discount' => 5,
        'expires_at' => null,
        'user_limit' => null,
        'is_valid' => true,
    ]);

    $this->from(route('admin.catalog.settings'))
        ->put(route('admin.promos.update', $promo), [
            'code' => 'NEW',
            'discount' => 15,
            'discount_type' => 'percentage',
            'expires_at' => null,
            'user_limit' => 100,
        ])
        ->assertRedirect(route('admin.catalog.settings'));
});

test('catalog brand store redirects when valid', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->from(route('admin.catalog.settings'))
        ->post(route('admin.brands.store'), [
            'name' => 'Acme',
            'slug' => 'acme-'.uniqid(),
        ])
        ->assertRedirect(route('admin.catalog.settings'));
});

test('catalog brand destroy redirects', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $brand = Brand::query()->create([
        'name' => 'Remove me',
        'slug' => 'remove-me-'.uniqid(),
    ]);

    $this->from(route('admin.catalog.settings'))
        ->delete(route('admin.brands.destroy', $brand))
        ->assertRedirect(route('admin.catalog.settings'));
});
