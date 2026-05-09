<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Promo;
use Inertia\Inertia;
use Inertia\Response;

class CatalogSettingController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/CatalogSettings', [
            'categories' => Category::withCount('products')->get(),
            'brands' => Brand::withCount('products')->get(),
            'promos' => Promo::all()->map(function ($promo) {
                return [
                    'id' => $promo->id,
                    'code' => $promo->code,
                    'discount' => $promo->discount_type === 'percentage' ? $promo->discount.'%' : $promo->discount.' USD',
                    'type' => $promo->discount_type,
                    'usage_limit' => $promo->user_limit,
                    'valid_until' => $promo->expires_at,
                    'status' => $promo->is_valid ? 'Active' : 'Expired',
                ];
            }),
        ]);
    }
}
