<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use Illuminate\Http\RedirectResponse;

class AddToCartController extends Controller
{
    public function __invoke(AddToCartRequest $request): RedirectResponse
    {
        $cart = session('cart',[]);
        $productId = $request->validated('product_id');
        $quantity = $request->validated('quantity');

        $cart[$productId] = ($cart[$productId] ?? 0) + $quantity;
        session(['cart' => $cart]);

        return back();
    }
}
