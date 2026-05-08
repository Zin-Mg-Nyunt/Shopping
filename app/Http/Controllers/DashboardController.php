<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('user.dashboard');
        }
    }

    public function adminDashboard(Request $request)
    {
        Gate::authorize('admin');

        return inertia('Admin/Dashboard');
    }

    public function userDashboard(Request $request)
    {
        Gate::authorize('user');

        return inertia('User/Dashboard', [
            'user' => $request->user()->load('orders'),
        ]);
    }
}
