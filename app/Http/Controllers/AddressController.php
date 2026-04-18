<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        return inertia('User/ShippingAddress', [
            'addresses' => $request->user()->addresses()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => 'nullable|string|max:255',
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:6',
            'is_default' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($request, $validated) {
            $this->checkAddressExists($request, $validated);
            $request->user()->addresses()->create($validated);
        });

        return redirect()->back()->with('success', 'Address created successfully');
    }

    public function update(Request $request, Address $address)
    {
        Gate::authorize('update', $address);
        $validated = $request->validate([
            'label' => 'nullable|string|max:255',
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:6',
            'is_default' => 'nullable|boolean',
        ]);

        $address->fill($validated);

        if ($address->isDirty()) {
            DB::transaction(function () use ($request, $validated, $address) {
                $this->checkAddressExists($request, $validated, $address->id);
                $address->save();
            });
            return back()->with('success', 'Address updated successfully');
        }

        return back()->with('info', 'You have not made any changes to update');
    }

    public function destroy(Address $address)
    {
        Gate::authorize('delete', $address);
        $address->delete();
        return back()->with('success', 'Address removed successfully');
    }

    private function checkAddressExists($request, $validated, $currentId = null)
    {
        $exists = $request->user()->addresses()
            ->where($request->only('street', 'city', 'postal_code'))
            ->when($currentId, function ($q, $currentId){
                return $q->where('id', '!=', $currentId);
            })
            ->exists();
            
        if ($exists) {
            throw ValidationException::withMessages(['address' => 'Address already exists']);
        }
        if (!empty($validated['is_default'])) {
            $request->user()->addresses()->update(['is_default' => false]);
        }
    }
}
