<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $admin=User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'role' => 'admin',
            'password' => 159357
        ]);
        $user=User::factory()->create([
            'name' => 'User',
            'email' => 'user@gmail.com',
            'role' => 'user',
            'password' => 159357
        ]);

        $categories=[
            'Electronics',
            'Household Appliances',
            'Gadgets',
            'Furniture',
            'Sports',
            'Clothes'
        ];

        foreach($categories as $category){
            Category::create([
                'name' => $category,
                'slug' => Str::slug($category)
            ]);
        };

        $brands=[
            'Apple',
            'Samsung',
            'LG',
            'Sony',
            'Dell',
            'HP',
            'Lenovo',
            'Nike',
            'Adidas',
            'Puma',
        ];
        foreach($brands as $brand){
            Brand::create([
                'name' => $brand,
                'slug' => Str::slug($brand)
            ]);
        };

        $response=Http::get('https://fakestoreapi.com/products?limit=10')->json();
        foreach($response as $product){
            $product=Product::create([
                'user_id' => $admin->id,
                'brand_id' => Brand::inRandomOrder()->first()->id,
                'thumbnail' => $product['image'],
                'name' => $product['title'],
                'slug' => Str::slug($product['title']),
                'description' => $product['description'],
                'price' => $product['price'],
                'discount_price' => $product['price']-5,
                'stock' => rand(10, 100),
            ]);
            $product->categories()->sync(Category::inRandomOrder()->limit(rand(1,3))->pluck('id'));
        }
    }
}
