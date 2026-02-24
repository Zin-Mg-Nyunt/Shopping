<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index(Request $request){
        return inertia('Admin/Customer/index');
    }
    public function userAddressBook(Request $request){
        return inertia('User/AddressBook');
    }
    public function saveAsDefaultAddress(Request $request){
        $validated = $request->validate([
            'phone' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'street_address' => 'required|string|max:255',
            'quarter_or_village' => 'required|string|max:255',
            'township' => 'required|string|max:255',
            'state_or_region' => 'required|string|max:255',
        ]);
        $user=$request->user();

        $defaultAddress = $user->addressBooks()->where('is_default', true)->first();
        if($defaultAddress && $defaultAddress->matches($validated)){
            return back()->with('error', 'Address already exists');
        }

        $existingAddress = $user->addressBooks()->where($validated)->first();
        $addressCount = $user->addressBooks()->count();

        if(!$existingAddress && $addressCount >= 10){
            return back()->with('error', 'You can only have 10 addresses');
        };

        DB::transaction(function() use ($validated, $user, $existingAddress){
            $user->addressBooks()->where('is_default', true)->update(['is_default' => false]);
            if($existingAddress){
                $existingAddress->update([
                    'is_default' => true,
                ]);
            }else{
                $user->addressBooks()->create(array_merge($validated, ['is_default' => true]));
            }
        });
        
        return back()->with('success', 'Address saved as default');
    }
}
