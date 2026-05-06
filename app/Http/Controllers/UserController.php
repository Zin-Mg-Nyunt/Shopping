<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    public function customerList()
    {
        $customers = User::where('role', 'user')->latest()->get();
        return inertia('Admin/CustomersList', [
            'customers' => $customers,
        ]);
    }
}
