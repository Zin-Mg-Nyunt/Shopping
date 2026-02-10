<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function getAllProducts($filter){
        $products=Product::filter($filter)
                        ->latest()
                        ->paginate(8)
                        ->withQueryString();
        $categories=Category::all();
        $brands=Brand::all();
        return compact('products', 'categories', 'brands', 'filter');
    }

    // Product Detail Page
    public function getProductDetail($product){
        $product->load('brand');
        $product->setAttribute('related_products',$this->getRelatedProducts($product));
        return $product;
    }
    private function getRelatedProducts($product){
        return cache()->remember('related_products_'.$product->id,now()->addMinutes(2),function() use($product) {
            return $this->hasCategories($product);
        });
    }
    private function hasCategories($product)
    {
        if($product->categories->isEmpty())
        {
            return $this->getLatestProducts($product);
        }

        $categoriesNames=$product->categories->pluck('name');
        $relatedProducts=Product::whereHas('categories',function($query) use($categoriesNames){
            $query->whereIn('name',$categoriesNames);
        })->where('id','!=',$product->id)->inRandomOrder()->limit(4)->get();

        if($relatedProducts->isEmpty())
        {
            $relatedProducts = $this->getLatestProducts($product);
        }elseif($relatedProducts->count() < 4)
        {
            $neededProducts= 4 - $relatedProducts->count();
            $extraProducts=Product::where('id','!=',$product->id)
                    ->whereNotIn('id',$relatedProducts->pluck('id'))
                    ->latest()
                    ->limit($neededProducts)
                    ->get();
                    
            $relatedProducts = $relatedProducts->merge($extraProducts);
        }

        return $relatedProducts;
    }
    private function getLatestProducts($product)
    {
        return Product::where('id','!=',$product->id)
            ->latest()
            ->limit(4)
            ->get();
    }

    // Create Product
    public function createProduct($validated){
        return DB::transaction(function() use($validated) {
            if($validated['thumbnail']){
                $path = $validated['thumbnail']->store('products','s3');
                $validated['thumbnail'] = $path;
            }
            [$categories_id,$validated]= $this->getNewCategoriesAndBrandIds($validated);
            $product = Product::create($validated);
            $product->categories()->sync($categories_id);
            return $product;  
        });
    }

    // Update Product
    public function updateProduct($product, $validated){
        return DB::transaction(function() use($product, $validated) {
            if($validated['thumbnail']){
                if($product->thumbnail){
                    Storage::disk('s3')->delete($product->getRawOriginal('thumbnail'));
                }
                $path = $validated['thumbnail']->store('products','s3');
                $validated['thumbnail'] = $path;
            }else if($validated['thumbnail'] == null){
                unset($validated['thumbnail']);
            }
            [$categories_id,$validated]= $this->getNewCategoriesAndBrandIds($validated);
            $product->update($validated);
            $product->categories()->sync($categories_id);
            return $product;
        });
    }

    private function getNewCategoriesAndBrandIds($validated){
        $categories=$validated['categories'];
        $brand = $validated['brand_id'];

        $categories_id=collect($categories)->map(function($category){
            return Category::firstOrCreate(
                ['slug' => $category['slug']],
                ['name' => $category['name']]
            );
        })->pluck('id')->toArray();

        if(is_array($brand)){
            $newBrand = Brand::firstOrCreate(
                ['slug' => $brand['slug']],
                ['name' => $brand['name']]
            );
            $validated['brand_id'] = $newBrand->id;
        }else if(empty($brand)){
            $noBrand = Brand::firstOrCreate(
                ['slug' => 'no-brand'],
                ['name' => 'No Brand']
            );
            $validated['brand_id'] = $noBrand->id;
        }
        unset($validated['categories']);
        return [$categories_id,$validated];
    }
}
