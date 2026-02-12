<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index(Request $request){
        return inertia('Admin/Coupon/index');
    }
}
