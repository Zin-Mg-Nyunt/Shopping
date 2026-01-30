<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request) {
        $filter=[
            'category' => $request->category,
            'search' => $request->search,
        ];
        $products=Product::filter($filter)->get();
        return inertia('Products',[
            'products' => $products,
            'categories' => Category::all(),
            'filter' => $filter,
        ]);
    }
}
