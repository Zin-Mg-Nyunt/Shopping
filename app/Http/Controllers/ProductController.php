<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request) {
        return inertia('Products/index',[
            'products' => Product::all(),
            'categories' => Category::all()
        ]);
    }
    public function show(Product $product) {
        return inertia('Products/show',[
            'product' => $product->load('brand')
        ]);
    }
}
