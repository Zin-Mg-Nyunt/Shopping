<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\User;

test('order list page', function(){
    $this->get(route('user.orders.list'))
        ->assertRedirect(route('login'));

    $user = User::factory()->create();
    $admin = User::factory()->create(['role'=>'admin']);

    Order::create([
        'user_id'=>$user->id,
        'order_number'=>'1234567890',
        'shipping_address'=>json_encode([
            'full_name'=>'John Doe',
            'phone'=>'1234567890',
            'street'=>'123 Main St',
            'city'=>'Yangon',
            'postal_code'=>'12345'
        ]),
        'payment_method'=>'cash',
        'total_quantity'=>2,
        'total_amount'=>200,
        'status'=>'pending',
        'promo_code'=>null,
        'created_at'=>now(),
        'updated_at'=>now(),
        'deleted_at'=>null,
    ]);

    $this->actingAs($user)
        ->get(route('user.orders.list'))
        ->assertOk()
        ->assertInertia(fn($page)=>$page
        ->component('User/OrderList')
        ->has('orders.links')
        ->has('orders.data', 1)
        ->has('orders.data.0.order_details')
        ->where('orders.data.0.order_number', '1234567890')
        ->has('statusCount')
        ->where('statusCount.pending', 1)
    );

    $this->actingAs($admin)
        ->get(route('admin.orders.list'))
        ->assertOk()
        ->assertInertia(fn($page)=>$page
        ->component('Admin/OrderList'))
        ->has('orders.links')
        ->has('orders.data', 1);
});

test('order store', function(){
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock'=>2]);
    $this->actingAs($user)
        ->post(route('order.store'), [
            'address_id'=>null,
            'shipping_address'=>[
                'full_name'=>'John Doe',
                'phone'=>'1234567890',
                'street'=>'123 Main St',
                'city'=>'Yangon',
                'postal_code'=>'12345'
            ],
            'payment_method'=>'cod',
            'total_amount'=>200,
            'product_ids'=>[$product->id],
            'quantity'=>[2],
            'promo_code'=>null,
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $this->assertEquals(0, $product->refresh()->stock);

    $this->assertDatabaseHas('orders',[
        'user_id'=>$user->id,
    ]);
    $this->assertDatabaseHas('order_details',[
        'order_id'=>1,
        'product_id'=>$product->id,
        'quantity'=>2,
    ]);
});

test('order can not store if not enough stock', function(){
    $user= User::factory()->create();
    $product = Product::factory()->create(['stock'=>5]);
    $this->actingAs($user)
        ->post(route('order.store'), [
            'address_id'=>null,
            'shipping_address'=>[
                'full_name'=>'John Doe',
                'phone'=>'1234567890',
                'street'=>'123 Main St',
                'city'=>'Yangon',
                'postal_code'=>'12345'
            ],
            'payment_method'=>'cod',
            'total_amount'=>200,
            'product_ids'=>[$product->id],
            'quantity'=>[8],
            'promo_code'=>null,
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('error', $product->title . ' is only ' . $product->stock . ' in stock');

    $this->assertEquals(5, $product->refresh()->stock);
    $this->assertDatabaseCount('orders', 0);
    $this->assertDatabaseCount('order_details', 0);
});
