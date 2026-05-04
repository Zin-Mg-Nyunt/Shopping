<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('brand', 'categories')
            ->when($request->user(), function ($query) use ($request) {
                $query->withExists('wishlistUsers as wishlisted', function ($q) use ($request) {
                    $q->where('user_id', $request->user()->id);
                });
            })
            ->latest()
            ->take(4)
            ->get();

        return inertia('Welcome', [
            'products' => $products,
        ]);
    }

    public function list(Request $request)
    {
        $products = Product::with('brand', 'categories')
            ->filterBy($request->all())
            ->when($request->user(), function ($query) use ($request) {
                $query->withExists('wishlistUsers as wishlisted', function ($q) use ($request) {
                    $q->where('user_id', $request->user()->id);
                });
            })
            ->paginate(4)
            ->withQueryString();

        return inertia('Products/List', [
            'products' => $products,
        ]);
    }

    public function adminList(Request $request)
    {
        $products = Product::with('brand', 'categories')
            ->filterBy($request->all())
            ->paginate(7)
            ->withQueryString();

        return inertia('Admin/Products', [
            'products' => $products,
        ]);
    }

    public function adminStore(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:products,slug'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'thumbnail' => ['required', 'image', 'max:2048'],
            'categories' => ['required', 'array', 'min:1'],
            'new_categories' => ['nullable', 'array', 'min:1'],
            'new_categories.*' => ['string', 'max:255'],
            'brand_id' => ['required', 'integer'],
            'new_brand' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            DB::transaction(function () use ($validated, $request) {
                if ($request->hasFile('thumbnail')) {
                    $path = $request->thumbnail->store('thumbnails', 's3');
                    if (! $path) {
                        throw new Exception('S3 Upload Failed! Check your AWS configuration.');
                    }
                    $validated['thumbnail'] = $path;
                }

                $catIds = Category::whereIn('id', $validated['categories'])->get()->pluck('id')->toArray();
                $newCatIds = [];
                if ($request->filled('new_categories')) {
                    $newCatIds = collect($validated['new_categories'])->map(function ($category) {
                        $newCategory = Category::firstOrCreate(
                            ['name' => $category],
                            ['slug' => Str::slug($category)],
                        );

                        return $newCategory->id;
                    })->toArray();
                }
                $finalCatIds = array_merge($catIds, $newCatIds);
                $validated['categories'] = $finalCatIds;

                if ($request->filled('new_brand')) {
                    $newBrand = Brand::firstOrCreate(
                        ['name' => $validated['new_brand']],
                        ['slug' => Str::slug($validated['new_brand'])],
                    );
                    $validated['brand_id'] = $newBrand->id;
                }

                $product = $request->user()->products()->create($validated);
                $product->categories()->attach($finalCatIds);
            });

            return redirect()->back()->with('success', 'Product created successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function adminUpdate(Request $request, Product $product)
    {
        $validated = $request->validate();
        dd($validated);
        try {
            DB::transaction(function() use($validated, $request, $product){
                if ($request->hasFile('thumbnail')) {
                    $originalPath = $product->getRawOriginal('thumbnail');
                    Storage::disk('s3')->delete($originalPath);
                    $path = $request->thumbnail->store('thumbnails', 's3');
                    if (! $path) {
                        throw new Exception('S3 Upload Failed! Check your AWS configuration.');
                    }
                    $validated['thumbnail'] = $path;
                }else{
                    unset($validated['thumbnail']);
                }
            });
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request): RedirectResponse
    {
        Gate::authorize('admin');

        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', Rule::exists('products', 'id')],
        ]);

        Product::whereIn('id', $validated['ids'])->delete();

        $message = count($validated['ids']) === 1
            ? 'Product deleted successfully.'
            : 'Selected products deleted successfully.';

        return redirect()->back()->with('success', $message);
    }

    public function detail(Product $product, Request $request)
    {
        $product->wishlisted = $request->user()
                                ? $product->wishlistUsers()->where('user_id', $request->user()->id)->exists()
                                : false;

        return inertia('Products/Detail', [
            'product' => $product,
        ]);
    }

    public function addWishlist(Product $product, Request $request)
    {
        $result = $request->user()->wishlistProducts()->toggle($product->id);
        $result['attached'] ? $message = 'Added to wishlist' : $message = 'Removed from wishlist';

        return redirect()->back()->with('success', $message);
    }

    public function showWishlist(Request $request)
    {
        $total = $request->user()->wishlistProducts()->count();
        $wishlist = $request->user()->wishlistProducts()
            ->with('brand')
            ->filterBy($request->all())
            ->paginate(6)
            ->withQueryString();

        return inertia('User/Wishlist', [
            'wishlist' => $wishlist,
            'total' => $total,
        ]);
    }

    public function removeWishlist(Product $product, Request $request)
    {
        $request->user()->wishlistProducts()->detach($product->id);

        return redirect()->back();
    }
}
