<?php

namespace App\Services;

use App\Models\Product;

class ProductService
{
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

    private function getLatestProducts($product)
    {
        return Product::where('id','!=',$product->id)
            ->latest()
            ->limit(4)
            ->get();
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
            Product::where('id','!=',$product->id)
                    ->whereNotIn('id',$relatedProducts->pluck('id'))
                    ->latest()
                    ->limit($neededProducts)
                    ->get()
                    ->merge($relatedProducts);
        }

        return $relatedProducts;
    }
}
