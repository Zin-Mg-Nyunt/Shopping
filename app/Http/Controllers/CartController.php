<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function addToCart(Request $r, AddToCartRequest $request )
    {
        $productId = $request->validated('product_id');
        $quantity = $request->validated('quantity');
        $user = $r->user();

        if($user){
            CartItem::updateOrCreate(
                ['user_id' => $user->id, 'product_id' => $productId],
                ['quantity' => DB::raw('quantity + '.$quantity)]
            );
        }else{
            $cart = session('cart',[]);
            $cart[$productId] = ($cart[$productId] ?? 0) + $quantity;
            session(['cart' => $cart]);
        }
        return back();
    }
    public function show(Request $request)
    {
        $items = $request->user()->cartItems()->with('product')->get();
        $address = $request->user()->addressBooks()->where('is_default', true)->first();
        return inertia('User/Cart/show', compact('items', 'address'));
    }
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:cart_items,id',
            'quantity' => 'required|integer|min:1',
        ]);
        $item=$request->user()->cartItems()->with('product')->find($request->id);
        if($request->quantity <= 0 || $request->quantity > $item->product->stock){
            return back();
        }
        $item->update(['quantity' => $request->quantity]);
        return back();
    }
    public function remove(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:cart_items,id',
        ]);
        $request->user()->cartItems()->find($request->id)->delete();
        return back();
    }
}
