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

    $response->assertForbidden();
});

test('admins can visit the admin dashboard', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $response = $this->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('Admin/Dashboard'));
});

test('shared auth user includes profile photo for inertia', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $response = $this->get(route('admin.dashboard'));

    $response->assertInertia(fn ($page) => $page->has('auth.user.profile_photo'));
});
