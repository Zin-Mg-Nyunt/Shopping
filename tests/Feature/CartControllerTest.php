<?php

use App\Models\Address;
use App\Models\Promo;
use App\Models\User;
use App\Models\Product;

describe('cart for guest user', function(){
    test('it shows session cart products', function(){
        $product=Product::factory()->create();

        $this->withSession(['cart'=>[$product->id => 4]])
            ->get(route('cart'))
            ->assertOk()
            ->assertInertia(fn($page)=>$page
            ->component('Products/Cart')            
            ->has('items', 1)
            ->where('items.0.id', $product->id)
            ->where('items.0.quantity', 4)
            ->has('addresses', 0)
            ->has('promos', 0)
        );
    });

    test('it shows nothing for empty session cart', function(){
        $this->get(route('cart'))
            ->assertOk()
            ->assertInertia(fn($page)=>$page
            ->component('Products/Cart')
            ->has('items', 0)
            ->has('addresses', 0)
            ->has('promos', 0)
        );
    });

    test('it can add product to session cart', function(){
        $product=Product::factory()->create();

        $this->post(route('cart.add'), [
                'product_id' => $product->id,
                'quantity' => 4,
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors()
            ->assertSessionHas('cart', [$product->id => 4])
            ->assertSessionHas('success', 'Product added to cart successfully');
    });

    test('it can update session cart product quantity', function(){
        $product=Product::factory()->create(['stock'=>10]);

        $this->post(route('cart.update'), [
                'product_id' => $product->id,
                'quantity' => 6,
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors()
            ->assertSessionHas('cart', [$product->id => 6]);
    });

    test('can change the stock quantity automatically if the quantity is greater than the stock', function(){
        $product=Product::factory()->create(['stock'=>10]);

        $this->post(route('cart.update'), [
                'product_id' => $product->id,
                'quantity' => 20,
            ])
            ->assertRedirect()
            ->assertSessionHas('cart', [$product->id => 10])
            ->assertSessionHasErrors(['error'=>'We have only '.$product->stock.' in stock.']);
    });

    test('can delete session cart product', function(){
        $product=Product::factory()->create();
        
        $this->withSession(['cart'=>[$product->id => 4]])
            ->delete(route('cart.destroy', $product->id))
            ->assertRedirect()
            ->assertSessionHasNoErrors()
            ->assertSessionMissing("cart.{$product->id}");
    });
});

describe('cart for authenticated user', function(){
    test('it shows user cart products', function(){
        $user=User::factory()->create();
        $productA=Product::factory()->create();
        $productB=Product::factory()->create();
        $user->cartProducts()->attach($productA->id, ['quantity'=>4]);
        $user->cartProducts()->attach($productB->id, ['quantity'=>2]);

        $this->actingAs($user)
            ->get(route('cart'))
            ->assertOk()
            ->assertInertia(fn($page)=>$page
            ->component('Products/Cart')
            ->has('items', 2)
            ->where('items.0.id', $productA->id)
            ->where('items.0.pivot.quantity', 4)
            ->where('items.1.id', $productB->id)
            ->where('items.1.pivot.quantity', 2)
            ->has('addresses', 0)
            ->has('promos', 0)
        );
    });

    test('it shows user addresses', function(){
        $user=User::factory()->create();
        $addressA=Address::factory()->create(['user_id'=>$user->id]);
        $addressB=Address::factory()->create(['user_id'=>$user->id]);

        $this->actingAs($user)
            ->get(route('cart'))
            ->assertOk()
            ->assertInertia(fn($page)=>$page
            ->component('Products/Cart')
            ->has('items', 0)
            ->has('addresses', 2)
            ->where('addresses.0.id', $addressA->id)
            ->where('addresses.1.id', $addressB->id)
            ->has('promos', 0)
        );  
    });

    test('it can add product to user cart table', function(){
        $user=User::factory()->create();
        $product=Product::factory()->create();

        $this->actingAs($user)
            ->post(route('cart.add'), [
                'product_id' => $product->id,
                'quantity' => 4,
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success', 'Product added to cart successfully');

        $this->assertDatabaseHas('carts',[
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 4,
        ]);
    });

    test('can update user cart product quantity', function(){
        $user=User::factory()->create();
        $product=Product::factory()->create(['stock'=>10]);

        $this->actingAs($user)
            ->post(route('cart.update'), [
                'product_id' => $product->id,
                'quantity' => 8,
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();
        
        $this->assertDatabaseHas('carts',[
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 8,
        ]);
    });

    test('can change the stock quantity automatically if the quantity is greater than the stock', function(){
        $user=User::factory()->create();
        $product = Product::factory()->create(['stock'=>10]);

        $this->actingAs($user)
            ->post(route('cart.update'), [
                'product_id' => $product->id,
                'quantity' => 20,
            ])
            ->assertRedirect()
            ->assertSessionHasErrors(['error'=>'We have only '.$product->stock.' in stock.']);

        $this->assertDatabaseHas('carts',[
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 10,
        ]);
    });

    test('can delete cart product', function(){
        $user=User::factory()->create();
        $product=Product::factory()->create();
        $user->cartProducts()->attach($product->id, ['quantity'=>4]);

        $this->actingAs($user)
            ->delete(route('cart.destroy', $product->id))
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('carts',[
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    });
});
    
test('promo are shown if valid', function(){
    $validPromo=Promo::factory()->create([
        'code'=>'VALID10',
        'discount_type'=>'percentage',
        'discount'=>10,
    ]);
    Promo::factory()->create([
        'code'=>'INVALID10',
        'discount_type'=>'percentage',
        'discount'=>10,
        'is_valid'=>false,
    ]);

    $this->get(route('cart'))
        ->assertOk()
        ->assertInertia(fn($page)=>$page
        ->component('Products/Cart')
        ->has('promos', 1)
        ->where('promos.0.id', $validPromo->id)
        ->where('promos.0.code', 'VALID10')
    );
});