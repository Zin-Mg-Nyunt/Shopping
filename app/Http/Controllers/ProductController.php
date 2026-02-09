<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function home(Request $request)
    {
        $filter = ['category' => $request->category];
        $categories = Category::all();
        $latestProducts = Product::filter($filter)->latest()->take(4)->get();

        return inertia('Home', compact('categories', 'filter', 'latestProducts'));
    }

    public function index(Request $request, ProductService $productService)
    {
        $filter = [
            'category' => $request->category,
            'search' => $request->search,
            'sortBy' => $request->sortBy,
            'price' => $request->price,
            'brand' => $request->brand,
        ];
        $products = $productService->getAllProducts($filter);

        return inertia('Products/index', $products);
    }

    public function show(Product $product, ProductService $productService)
    {
        return inertia('Products/show', [
            'product' => $productService->getProductDetail($product),
        ]);
    }

    public function adminProducts(Request $request, ProductService $productService)
    {
        $filter = [
            'category' => $request->category,
            'search' => $request->search,
            'sortBy' => $request->sortBy,
            'brand' => $request->brand,
        ];
        $products = $productService->getAllProducts($filter);

        return inertia('Admin/Products/index', $products);
    }

    public function adminDeleteProduct(Product $product)
    {
        $product->delete();

        return back();
    }

    public function adminCreateProduct()
    {
        $brands = Brand::query()->orderBy('name')->get();
        $categories = Category::query()->orderBy('name')->get();

        return inertia('Admin/Products/create', [
            'brands' => $brands,
            'categories' => $categories,
        ]);
    }

    public function adminStoreProduct(Request $request, ProductService $productService){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'categories' => 'required|array',
            'brand_id' => 'nullable',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $validated['user_id'] = $request->user()->id;

        $productService->createProduct($validated);
        return redirect()->route('admin.products');
    }
}
