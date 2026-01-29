<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
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

        $admin->products()->create([
            'brand_id' => Brand::where('name', 'Apple')->first()->id,
            'thumbnail' => 'https://via.placeholder.com/150',
            'name' => 'iPhone 15',
            'slug' => Str::slug('iPhone 15'),
            'description' => 'The latest iPhone from Apple with a 6.1-inch display, A17 Pro chip, 12MP camera, and 5G support.',
            'price' => 1000,
            'discount_price' => 900,
            'stock' => 100,
        ]);
    }
}
