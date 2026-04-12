<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
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

    public function cartUsers(){
        return $this->belongsToMany(User::class, 'carts')
                    ->withPivot('quantity');
    }

    public function wishlistUsers(){
        return $this->belongsToMany(User::class, 'wishlists')->withTimestamps();
    }

    public function carts(){
        return $this->hasMany(Cart::class,'product_id');
    }

    public function scopeFilterBy($query, $filters){
        return $query->when($filters['category']??false, function($query, $slug){
            $query->whereHas('categories',function($query) use($slug){
                $query->where('slug',$slug);
            });
        })
        ->when($filters['search']??false, function($query,$search){
            $query->where(function($query) use($search){
                $query->where('title','like','%'.$search.'%')
                        ->orWhere('description','like','%'.$search.'%');
            });
        })
        ->when($filters['brand']??false, function($query,$slug){
            $query->whereHas('brand',function($query) use($slug){
                $query->where('slug',$slug);
            });
        })
        ->when($filters['price']??false, function($query,$price){
            $query->whereBetween('price',[0,$price]);
        })
        ->when($filters['sort']??false, function($query,$sort){
            match($sort){
                'newest' => $query->latest(),
                'low-to-high' => $query->orderBy('price','asc'),
                'high-to-low' => $query->orderBy('price','desc'),
                default => $query->latest(),
            };
        },function($query){
            $query->latest();
        });
    }
}
