<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    public function index(Request $request){
        $products = Product::with('brand','categories')
                    ->when($request->user(), function ($query) use ($request){
                        $query->withExists('wishlistUsers as wishlisted', function($q) use ($request){
                            $q->where('user_id',$request->user()->id);
                        });
                    })
                    ->latest()
                    ->take(4)
                    ->get();
        return inertia('Welcome', [
            'products' => $products
        ]);
    }

    public function list(Request $request){
        $products = Product::query()
                    ->with('brand','categories')
                    ->filterBy($request->all())
                    ->when($request->user(), function ($query) use ($request){
                        $query->withExists('wishlistUsers as wishlisted', function($q) use ($request){
                            $q->where('user_id',$request->user()->id);
                        });
                    })
                    ->paginate(4)
                    ->withQueryString();
        return inertia('Products/List', [
            'products' => $products
        ]);
    }

    public function detail(Product $product, Request $request){
        $product->wishlisted = $request->user() 
                                ? $product->wishlistUsers()->where('user_id',$request->user()->id)->exists() 
                                : false;
        return inertia('Products/Detail',[
            'product' => $product
        ]);
    }

    public function addToCart(Request $request){
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);
        if($request->user()){
            Cart::updateOrCreate([
                'user_id' => $request->user()->id,
                'product_id' => $request->product_id,
            ],[
                'quantity' => DB::raw('quantity + '.$request->quantity)
            ]);
        }else{
            $cart = session()->get('cart',[]);
            $cart[$request->product_id] = ($cart[$request->product_id] ?? 0) + $request->quantity;
            session()->put('cart',$cart);
        }
        return redirect()->back()->with('success', 'Product added to cart successfully');
    }

    public function addWishlist(Product $product, Request $request){
        if(Gate::allows('add-to-wishlist')){
            $result=$request->user()->wishlistProducts()->toggle($product->id);
            $result['attached'] ? $message = 'Added to wishlist' : $message = 'Removed from wishlist';
            return redirect()->back()->with('success', $message);
        }
        return redirect()->route('login');
    }
}
