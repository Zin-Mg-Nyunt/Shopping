<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request) {
        // dd($request->all());
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
}
