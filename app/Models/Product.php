<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

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
        });
    }
}
