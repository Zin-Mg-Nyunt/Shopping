<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;

test('guest users can view product list in Welcome page without wishlisted', function(){

    Product::factory()->count(5)->create();
    $latestProduct = Product::latest()->first();    

    $response=$this->get(route('home'));
    $response->assertOk()
            ->assertInertia(fn($page)=>$page
            ->component('Welcome')
            ->has('products', 4)
            ->has('products.0.brand')
            ->has('products.0.categories')
            ->missing('products.0.wishlisted')
            ->where('products.0.id', $latestProduct->id)
        );
});

test('auth user can view product list in Welcome page with wishlisted', function(){
    $user = User::factory()->create();
    Product::factory(5)->create();
    $latestProduct=Product::latest()->first();
    $user->wishlistProducts()->attach($latestProduct->id);

    $this->actingAs($user)
        ->get(route('home'))
        ->assertOk()
        ->assertInertia(fn($page)=>$page
        ->component('Welcome')
        ->has('products', 4)
        ->where('products.0.wishlisted', true)
        ->where('products.1.wishlisted', false)
        ->where('products.0.id', $latestProduct->id)
    );
});

test('user can view and filter products in product list page', function(){
    $user = User::factory()->create();
    Brand::factory()->create(['name'=>'Apple', 'slug'=>'apple']);
    Category::factory()->create(['name'=>'Smartphones', 'slug'=>'smartphones']);
    Product::factory(3)->create(['brand_id'=>1,'title'=>'Apple iPhone 14'])->each(function($p){
        $p->categories()->attach(1);
    });
    Product::factory(3)->create();

    $this->actingAs($user)
        ->get(route('products.list',['brand'=>'apple', 'category'=>'smartphones', 'search'=>'iphone']))
        ->assertOk()
        ->assertInertia(fn($page)=>$page
        ->component('Products/List')
        ->has('products.links')
        ->has('products.data', 3)
        ->where('products.data.0.wishlisted', false)
        ->where('products.first_page_url', fn($url)=>str_contains($url,'brand=apple'))
    );
});

test('user can view product detail page', function(){
    $user = User::factory()->create();
    $product = Product::factory()->create(['slug'=>'apple-iphone-14']);
    $user->wishlistProducts()->attach($product->id);
    
    $this->actingAs($user)
        ->get(route('product.detail', $product->slug))
        ->assertOk()
        ->assertInertia(fn($page)=>$page
        ->component('Products/Detail')
        ->has('product')
        ->where('product.id', $product->id)
        ->where('product.wishlisted', true)
    );
});

test('auth user can toggle product to wishlist', function($url){
    $user = User::factory()->create();
    $productA = Product::factory()->create(['slug'=>'apple-iphone-14']);
    $productB = Product::factory()->create(['slug'=>'samsung-galaxy-s23']);
    $user->wishlistProducts()->attach($productB->id);

    $this->actingAs($user)
        ->from($url)
        ->post(route('wishlist.add',$productA->id))
        ->assertRedirect($url)
        ->assertSessionHas('success', 'Added to wishlist');

    $this->actingAs($user) 
        ->post(route('wishlist.add',$productB->id))
        ->assertRedirect($url)
        ->assertSessionHas('success', 'Removed from wishlist');

    $this->assertDatabaseHas('wishlists',[
            'user_id'=>$user->id,
            'product_id'=>$productA->id
        ])
        ->assertDatabaseMissing('wishlists',[
            'user_id'=>$user->id,
            'product_id'=>$productB->id
        ])
        ->assertDatabaseCount('wishlists', 1);
})->with([
    fn()=>route('home'),
    fn()=>route('products.list'),
    fn()=>route('product.detail',['apple-iphone-14'])
]);

test('guest is redirected to login when trying to wishlist and redirect back to previous page', function($url){
    $product = Product::factory()->create(['slug'=>'apple-iphone-14']);
    $user = User::factory()->create();

    $this->from($url)
        ->post(route('wishlist.add',$product->id))
        ->assertRedirect(route('login'));

    $this->withSession(['url.intended'=>$url])
        ->post(route('login.store'),[
            'email'=>$user->email,
            'password'=>'password',
        ])
        ->assertRedirect($url);
})->with([
    fn()=>route('home'),
    fn()=>route('products.list'),
    fn()=>route('product.detail',['apple-iphone-14'])
]);