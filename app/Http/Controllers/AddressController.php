<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index(Request $request){
        return inertia('User/ShippingAddress',[
            'addresses' => $request->user()->addresses()->get()
        ]);
    }

    public function store(Request $request){
        $request->validate([
            'label' => 'nullable|string|max:255',
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:6',
            'is_default' => 'nullable|boolean',
        ]);
        $exists = $request->user()->addresses()
                    ->where([
                        'street' => $request->street,
                        'city' => $request->city,
                        'postal_code' => $request->postal_code,
                    ])
                    ->exists();
        if($exists){
            return back()->withErrors(['address'=>'Address already exists']);
        }
        if($request->is_default){
            $request->user()->addresses()->update(['is_default'=>false]);
        }
        $request->user()->addresses()->create($request->all());
        return redirect()->back();
    }
}
