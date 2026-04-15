<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request){
        if($request->user()->role === 'admin'){
            return inertia('Admin/Dashboard');
        } else {
            return inertia('User/Dashboard',[
                'orders' => $request->user()->orders()->with('orderDetails')->get()
            ]);
        }
    }
}
