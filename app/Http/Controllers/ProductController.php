<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function home(Request $request){
        $filter=['category'=>$request->category];
        $categories=Category::all();
        $latestProducts = Product::filter($filter)->latest()->take(4)->get();
        return inertia('Home',compact('categories','filter','latestProducts'));
    }
    public function index(Request $request) {
        $filter=[
            'category' => $request->category,
            'search' => $request->search,
            'sortBy' => $request->sortBy,
            'price' => $request->price,
            'brand' => $request->brand,
        ];
        $products=Product::filter($filter)
                        ->latest()
                        ->paginate(8)
                        ->withQueryString();
        $categories=Category::all();
        $brands=Brand::all();
        return inertia('Products/index', compact('products', 'categories', 'brands', 'filter'));
    }
    public function show(Product $product, ProductService $productService) {
        return inertia('Products/show',[
            'product' => $productService->getProductDetail($product)
        ]);
    }
}
