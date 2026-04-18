<?php

use App\Models\Address;
use App\Models\User;

test('user can see their addresses in shipping address page', function () {
    $user=User::factory()->create();
    Address::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get(route('user.shipping.address'))
        ->assertOk()
        ->assertInertia(fn($page)=>$page
            ->component('User/ShippingAddress')
            ->has('addresses', 1)
        );
});


test('user can create a new address', function(){
    $user = User::factory()->create();
    $data = Address::factory()->make([
        'user_id' => $user->id,
        'phone'=>'1234567890',
        'street' => '123 Main St',
        'postal_code' => '12345'
    ])->toArray();

    $this->actingAs($user)
        ->from(route('user.shipping.address'))
        ->post(route('user.address.store'),$data)
        ->assertRedirect(route('user.shipping.address'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('addresses', ['street' => $data['street']]);
});

test('user can not create duplicate address', function(){
    $user = User::factory()->create();
    Address::factory()->create([
        'user_id'=>$user->id,
        'street'=>'123 Main St',
        'city'=>'Yangon',
        'postal_code'=>'12345'
    ]);

    $this->actingAs($user)
        ->from(route('user.shipping.address'))
        ->post(route('user.address.store'),[
            'full_name'=>'John Doe',
            'phone'=>'1234567890',
            'street'=>'123 Main St',
            'city'=>'Yangon',
            'postal_code'=>'12345'
        ])
        ->assertRedirect(route('user.shipping.address'))
        ->assertSessionHasErrors(['address']);
    
    $this->assertDatabaseCount('addresses', 1);
});

test('user can create only one default address', function(){
    $user = User::factory()->create();
    $oldAddress=Address::factory()->create([
        'user_id'=>$user->id,
        'is_default'=>true
    ]);
    $this->actingAs($user)
        ->post(route('user.address.store'),[
            'full_name'=>'John Doe',
            'phone'=>'1234567890',
            'street'=>'123 Main St',
            'city'=>'Yangon',
            'postal_code'=>'12345',
            'is_default'=>true
        ]);

    $this->assertDatabaseHas('addresses', ['id'=>$oldAddress->id, 'is_default'=>false]);
    $this->assertDatabaseHas('addresses', ['full_name'=>'John Doe', 'is_default'=>true]);
});

test('user can not create other user address', function(){
    $userA = User::factory()->create();
    $userB = User::factory()->create();

    $data = [
        'user_id'=>$userB->id,
        'full_name'=>'John Doe',
        'phone'=>'1234567890',
        'street'=>'123 Main St',
        'city'=>'Yangon',
        'postal_code'=>'12345',
        'is_default'=>false
    ];

    $this->actingAs($userA)
        ->post(route('user.address.store'), $data);

    $this->assertDatabaseHas('addresses', ['user_id'=>$userA->id]);
    $this->assertDatabaseMissing('addresses', ['user_id'=> $userB->id]);
});

test('user can delete their own address', function(){
    $user = User::factory()->create();
    $address = Address::factory()->create([
        'user_id'=>$user->id,
        'full_name'=>'John Doe',
        'phone'=>'1234567890',
        'street'=>'123 Main St',
        'city'=>'Yangon',
        'postal_code'=>'12345',
        'is_default'=>false
    ]);

    $this->actingAs($user)
        ->from(route('user.shipping.address'))
        ->delete(route('user.address.destroy', $address))
        ->assertRedirect(route('user.shipping.address'))
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('addresses', ['id'=>$address->id]);
});

test('user can not delete other user address',function(){
    $userA = User::factory()->create();
    $userB = User::factory()->create();
    $address = Address::factory()->create([
        'user_id'=>$userB->id,
        'full_name'=>'John Doe',
        'phone'=>'1234567890',
        'street'=>'123 Main St',
        'city'=>'Yangon',
        'postal_code'=>'12345',
        'is_default'=>false
    ]);

    $this->actingAs($userA)
        ->delete(route('user.address.destroy', $address->id));

    $this->assertDatabaseHas('addresses', ['id'=>$address->id]);
});