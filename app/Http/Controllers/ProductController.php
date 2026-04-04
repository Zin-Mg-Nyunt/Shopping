<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    public function index(){
        $products = Product::with('brand','categories')->latest()->take(4)->get();
        return inertia('Welcome', [
            'products' => $products
        ]);
    }

    public function list(){
        $products = Product::with('brand','categories')
                    ->latest()
                    ->paginate(8)
                    ->withQueryString();
        return inertia('Products/List', [
            'products' => $products
        ]);
    }
}
