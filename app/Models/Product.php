<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function scopeFilter($query,$filter){
        return $query->when($filter['category'],function($query,$category){
            $query->whereHas('categories',function($query) use ($category){
                $query->where('slug',$category);
            });
        })->when($filter['search'],function($query,$search){
            $query->where(function($query) use ($search){
                $query->where('name','like','%'.$search.'%')
                ->orWhere('description','like','%'.$search.'%');
            });
        })->when($filter['sortBy'],function($query,$sortBy){
            $query->when($sortBy === 'oldest', function($query){
                $query->orderBy('created_at','asc');
            })->when($sortBy === 'price_asc',function($query){
                $query->orderBy('discount_price','asc');
            })->when($sortBy === 'price_desc',function($query){
                $query->orderBy('discount_price','desc');
            });
        });
    }
}
