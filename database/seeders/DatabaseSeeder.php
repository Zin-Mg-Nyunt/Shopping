<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Brand::truncate();
        Category::truncate();
        Product::truncate();
        User::truncate();
        Schema::enableForeignKeyConstraints();

        $users = [
            ['name'=>'Admin','email'=>'admin@gmail.com','password'=>'password','role'=>'admin'],
            ['name'=>'User','email'=>'user@gmail.com','password'=>'password','role'=>'user']
        ];

        $users=collect($users)->map(function($user){
            return User::create([
                'name'=>$user['name'],
                'email'=>$user['email'],
                'password'=>$user['password'],
                'role'=>$user['role'],
            ]);
        });

        $admin = $users->firstWhere('role','admin');
        
        $brands = [
            ['name' => 'Apple', 'slug' => 'apple'],
            ['name' => 'Nike', 'slug' => 'nike'],
            ['name' => 'Adidas', 'slug' => 'adidas'],
            ['name' => 'Puma', 'slug' => 'puma'],
            ['name' => 'Converse', 'slug' => 'converse'],
            ['name' => 'Vans', 'slug' => 'vans'],
        ];

        $brands=collect($brands)->map(function($brand){
            return Brand::create([
                'name' => $brand['name'],
                'slug' => $brand['slug'],
            ]);
        });

        $categories = [
            ['name' => 'Electronics', 'slug' => 'electronics'],
            ['name' => 'Clothing', 'slug' => 'clothing'],
            ['name' => 'Shoes', 'slug' => 'shoes'],
            ['name' => 'Accessories', 'slug' => 'accessories'],
        ];
        
        $categories=collect($categories)->map(function($category){
            return Category::create([
                'name' => $category['name'],
                'slug' => $category['slug'],
            ]);
        });

        Product::factory(10)->create(
            [
                'user_id'=>$admin->id,
                'brand_id'=>fn()=>$brands->random()->id
            ]
        )->each(function($product) use($categories){
            $date = now()->subDays(rand(1,5));
            $product->update([
                'created_at'=>$date,
                'updated_at'=>$date,
            ]);
            $product->categories()->attach($categories->random(rand(1,3))->pluck('id'));
        });
    }
}
