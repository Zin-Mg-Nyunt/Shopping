<?php

use App\Models\Address;
use App\Models\User;

test('verified user can delete their own shipping address', function () {
    $user = User::factory()->create();
    $address = Address::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->delete(route('user.address.destroy', $address))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(Address::query()->whereKey($address->id)->exists())->toBeFalse();
});

test('user cannot delete another users shipping address', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $address = Address::factory()->create(['user_id' => $owner->id]);

    $this->actingAs($intruder)
        ->delete(route('user.address.destroy', $address))
        ->assertForbidden();

    expect(Address::query()->whereKey($address->id)->exists())->toBeTrue();
});
