<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $guarded = ['id'];

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cartUsers()
    {
        return $this->belongsToMany(User::class, 'carts')
            ->withPivot('quantity');
    }

    public function wishlistUsers()
    {
        return $this->belongsToMany(User::class, 'wishlists')->withTimestamps();
    }

    public function carts()
    {
        return $this->hasMany(Cart::class, 'product_id');
    }

    public function scopeFilterBy($query, $filters)
    {
        return $query->when($filters['category'] ?? false, function ($query, $slug) {
            $query->whereHas('categories', function ($query) use ($slug) {
                $query->where('slug', $slug);
            });
        })
            ->when($filters['search'] ?? false, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%');
                });
            })
            ->when($filters['brand'] ?? false, function ($query, $slug) {
                $query->whereHas('brand', function ($query) use ($slug) {
                    $query->where('slug', $slug);
                });
            })
            ->when($filters['price'] ?? false, function ($query, $price, $startPrice = 0) {
                match ($price) {
                    'under100' => $query->whereBetween(DB::raw('coalesce(discount_price, price)'), [$startPrice, 100]),
                    '100to500' => $query->whereBetween(DB::raw('coalesce(discount_price, price)'), [100, 500]),
                    'over500' => $query->where(DB::raw('coalesce(discount_price, price)'), '>', 500),
                    default => $query->whereBetween(DB::raw('coalesce(discount_price, price)'), [$startPrice, $price]),
                };
            })
            ->when($filters['stock'] ?? false, function ($query, $stock) {
                match ($stock) {
                    'low' => $query->where('stock', '<', 10),
                    'out' => $query->where('stock', 0),
                    default => $query->where('stock', '>', 0),
                };
            })
            ->when($filters['sort'] ?? false, function ($query, $sort) {
                match ($sort) {
                    'newest' => $query->latest(),
                    'low-to-high' => $query->orderByRaw('coalesce(discount_price, price) asc'),
                    'high-to-low' => $query->orderByRaw('coalesce(discount_price, price) desc'),
                    default => $query->latest(),
                };
            }, function ($query) {
                $query->latest();
            });
    }

    protected function thumbnail(): Attribute
    {
        return Attribute::make(
            get: static fn (?string $value) => blank($value)
                ? null
                : (str_starts_with($value, 'http')
                    ? $value
                    : Storage::url($value))
        );
    }

    /**
     * Unit price charged for this product for the given user at checkout.
     */
    public function unitPriceFor(?User $user): float
    {
        $standard = (float) ($this->discount_price ?? $this->price);

        if ($user !== null && $user->isLoyaltyCustomer() && $this->loyal_price !== null) {
            return (float) $this->loyal_price;
        }

        return $standard;
    }
}
