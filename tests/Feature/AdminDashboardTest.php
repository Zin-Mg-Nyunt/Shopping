<?php

use App\Models\User;

test('guests cannot visit the admin dashboard', function () {
    $response = $this->get(route('admin.dashboard'));

    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the admin dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('Admin/Dashboard'));
});
