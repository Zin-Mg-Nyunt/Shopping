<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CartController extends Controller
{
    public function show(Request $request)
    {
        return inertia('User/Cart/show');
    }
}
