<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
class ProductController extends Controller
{
    public function index(){
        $products = Product::with('brand','categories')->latest()->take(4)->get();
        return inertia('Welcome', [
            'products' => $products
        ]);
    }

    public function list(Request $request){
        $products = Product::with('brand','categories')
                    ->filterBy($request->all())
                    ->latest()
                    ->paginate(4)
                    ->withQueryString();
        return inertia('Products/List', [
            'products' => $products
        ]);
    }
}
