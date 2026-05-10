<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PromoController extends Controller
{
    /**
     * @return array<string, mixed>
     */
    private function promoValidationRules(Request $request): array
    {
        $discountRules = ['required', 'numeric', 'min:0'];

        if ($request->discount_type === 'percentage') {
            $discountRules[] = 'max:100';
        }

        return [
            'code' => ['required', 'string', 'max:64'],
            'discount' => $discountRules,
            'discount_type' => ['required', Rule::in(['percentage', 'fixed'])],
            'expires_at' => ['nullable', 'date'],
            'user_limit' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->promoValidationRules($request));

        Promo::create($validated);

        return redirect()->back()->with('success', 'Promo updated successfully');
    }

    public function update(Request $request, Promo $promo): RedirectResponse
    {
        $validated = $request->validate($this->promoValidationRules($request));

        $promo->update($validated);

        return redirect()->back()->with('success', 'Promo updated successfully');
    }

    public function destroy(Promo $promo): RedirectResponse
    {
        $promo->delete();

        return redirect()->back()->with('success', 'Promo deleted successfully');
    }
}
