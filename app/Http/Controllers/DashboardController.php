<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request) {
        if($request->user()->role === 'admin'){
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('user.dashboard');
        }
    }
    public function admin(Request $request) {
        dd('admin dashboard');
    }
    public function user(Request $request) {
        dd('user dashboard');
    }
}
