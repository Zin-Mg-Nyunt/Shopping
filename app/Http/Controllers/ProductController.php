<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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
