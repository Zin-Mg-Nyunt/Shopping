<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request){
        return inertia('Admin/Customer/index');
    }
    public function userAddressBook(Request $request){
        return inertia('User/AddressBook');
    }
}
