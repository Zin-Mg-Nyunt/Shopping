<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use App\Http\Requests\DestroyProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\StoreProductRequest;
use App\Services\ProductService;

class ProductController extends Controller
{
    public function index(ProductService $productService)
    {
        return inertia('Welcome', [
            'products' => $productService->index(),
        ]);
    }

    public function list(ProductService $productService)
    {
        return inertia('Products/List', [
            'products' => $productService->list(),
        ]);
    }

    public function adminList(ProductService $productService)
    {
        return inertia('Admin/Products', [
            'products' => $productService->adminList(),
        ]);
    }

    public function adminStore(StoreProductRequest $request, ProductService $productService)
    {
        $validated = $request->validated();
        try {
            $productService->store($request, $validated);
            return redirect()->back()->with('success', 'Product created successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function adminUpdate(UpdateProductRequest $request, Product $product, ProductService $productService)
    {
        $validated = $request->validated();
        try {
            $productService->update($request, $product, $validated);
            return redirect()->back()->with('success', 'Product updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy(DestroyProductRequest $request, ProductService $productService)
    {
        $validated = $request->validated();
        $message = $productService->destroy($validated);
        return redirect()->back()->with('success', $message);
    }

    public function detail(Product $product, Request $request)
    {
        $product->wishlisted = $request->user()
                                ? $product->wishlistUsers()
                                    ->where('user_id', $request->user()->id)
                                    ->exists()
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
