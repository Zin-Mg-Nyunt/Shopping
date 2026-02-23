<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;
    protected $guarded = ['id'];

    public function brand(){
        return $this->belongsTo(Brand::class);
    }
    public function categories(){
        return $this->belongsToMany(Category::class);
    }
    public function reviews(){
        return $this->hasMany(Review::class);
    }
    public function cartItems(){
        return $this->hasMany(CartItem::class);
    }

    public function scopeFilter($query,$filter){
        return $query->when($filter['category']??false,function($query,$category){
            $query->whereHas('categories',function($query) use ($category){
                $query->where('slug',$category);
            });
        })->when($filter['brand']??false,function($query,$brand){
            $query->whereHas('brand',function($query) use($brand){
                $query->where('slug',$brand);
            });
        })->when($filter['search']??false,function($query,$search){
            $query->where(function($query) use ($search){
                $query->where('name','like','%'.$search.'%')
                ->orWhere('description','like','%'.$search.'%');
            });
        })->when($filter['sortBy']??false,function($query,$sortBy){
            $query->when($sortBy === 'oldest', function($query){
                $query->orderBy('created_at','asc');
            })->when($sortBy === 'price_asc',function($query){
                $query->orderByRaw('COALESCE(discount_price,price) asc');
            })->when($sortBy === 'price_desc',function($query){
                $query->orderByRaw('COALESCE(discount_price,price) desc');
            });
        })->when($filter['price']??false,function($query,$price){
            $query->whereRaw('COALESCE(discount_price,price) <= ?',[$price]);
        });
    }
    public function getThumbnailAttribute($value){
        if(!$value){
            return null;
        }
        return str_starts_with($value,'http') ? $value : Storage::disk('s3')->url($value);
    }
}
