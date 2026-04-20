<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\Promo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index(Request $request)  {
        $items = [];
        $user = $request->user();
        
        if($user){
            $items = $user->cartProducts()->with('brand')->get();
        }else{
            $cart=session()->get('cart',[]);
            $items=Product::whereIn('id',array_keys($cart))
                            ->with('brand')
                            ->get()
                            ->map(function($product) use ($cart){
                                $product->quantity=$cart[$product->id];
                                return $product;
                            });
        }
        return inertia('Products/Cart', [
            'items' => $items,
            'addresses' => $user ? $user->addresses()->get() : [],
            'promos' => Promo::where('is_valid',true)->get()
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

    public function update(Request $request)
    {
        $validated=$request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $user = $request->user();
        $finalQuantity = $validated['quantity'];
        $hasError = false;

        if($validated['quantity'] > $product->stock){
            $finalQuantity = $product->stock;
            $hasError = true;
        }

        if($user){
            $user->carts()->updateOrCreate(['product_id'=>$product->id],['quantity'=>$finalQuantity]);
        }else{
            $this->updateSessionCart($product->id, $finalQuantity);
        }

        if($hasError){
            return redirect()->back()->withErrors(['error'=>'We have only '.$product->stock.' in stock.']);
        }

        return back();
    }

    private function updateSessionCart($product_id, $quantity){
        $cart=session()->get('cart',[]);
        $cart[$product_id]=$quantity;
        session()->put('cart',$cart);
    }

    public function destroy($id,Request $request){
        $user = $request->user();
        if($user){
            $user->carts()->where('product_id',$id)->forceDelete();
        }else{
            session()->forget("cart.{$id}");
        }
        return redirect()->back();
    }
}
