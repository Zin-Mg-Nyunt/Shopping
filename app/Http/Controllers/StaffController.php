<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function staffManagement(Request $request){
        return inertia('Admin/Staff/staffManagement');
    }
}
