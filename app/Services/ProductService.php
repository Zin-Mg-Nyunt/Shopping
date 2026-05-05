<?php
namespace App\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductService
{

    private function getProductsQuery()
    {
        $request = request();
        return Product::with('brand', 'categories')
            ->when($request->user(), function ($query) use ($request) {
                $query->withExists(['wishlistUsers as wishlisted' => fn($q) => $q->where('user_id',$request->user()->id)]);
            });
    }

    public function index()
    {
        return $this->getProductsQuery()
            ->latest()
            ->take(4)
            ->get();
    }

    public function list()
    {
        $request = request();
        return $this->getProductsQuery()
            ->filterBy($request->all())
            ->paginate(4)
            ->withQueryString();
    }

    public function adminList()
    {
        $request = request();
        return Product::with('brand', 'categories')
            ->filterBy($request->all())
            ->paginate(7)
            ->withQueryString();
    }

    public function store(Request $request, array $validated)
    {
        try {
            [$oldPath,$newPath,$error] = $this->storeThumbnail($request);

            if($error) throw new Exception($error);

            if($newPath) $validated['thumbnail'] = $newPath;

            $product = DB::transaction(function () use ($validated, $request) {
                $validated = $this->handleCategoriesAndBrand($validated);
                $product = $request->user()->products()->create($validated);
                $product->categories()->attach($validated['categories']);
            });
            return $product;
        } catch (Exception $e) {
            if($newPath) Storage::disk('s3')->delete($newPath);
            throw $e;
        }
    }
    
    public function update(Request $request, Product $product, array $validated)
    {
        try {
            [$oldPath,$newPath,$error] = $this->storeThumbnail($request, $product);

            if($error) throw new Exception($error);

            if ($newPath) $validated['thumbnail'] = $newPath;
            else unset($validated['thumbnail']);

            $product = DB::transaction(function() use($validated, $product){
                $validated = $this->handleCategoriesAndBrand($validated);
                $product->update($validated);
                $product->categories()->sync($validated['categories']);
            });

            if ($oldPath) Storage::disk('s3')->delete($oldPath);
            return $product;
        } catch (Exception $e) {
            if($newPath) Storage::disk('s3')->delete($newPath);
            throw $e;
        }
    }

    private function storeThumbnail(Request $request, $product = null)
    {
        $oldPath = null;
        $newPath = null;
        try {
            if($request->hasFile('thumbnail')) {
                if ($product && $product->thumbnail) {
                    $oldPath = $product->getRawOriginal('thumbnail');
                }
                $newPath = $request->thumbnail->store('thumbnails', 's3');
            }
            return [$oldPath,$newPath, null];
        } catch (Exception $e) {
            return [$oldPath,$newPath, $e->getMessage()];
        }
    }

    private function handleCategoriesAndBrand(array $validated)
    {
        $catIds = Category::whereIn('id', $validated['categories'])->get()->pluck('id')->toArray();
        $newCatIds = [];
        if (isset($validated['new_categories'])) {
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

        if (isset($validated['new_brand'])) {
            $newBrand = Brand::firstOrCreate(
                ['name' => $validated['new_brand']],
                ['slug' => Str::slug($validated['new_brand'])],
            );
            $validated['brand_id'] = $newBrand->id;
        }
        return $validated;
    }

    public function destroy(array $validated)
    {
        Product::whereIn('id', $validated['ids'])->delete();

        $message = count($validated['ids']) === 1
            ? 'Product deleted successfully.'
            : 'Selected products deleted successfully.';

        return $message;
    }
}