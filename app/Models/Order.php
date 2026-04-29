<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];

    protected function casts(){
        return [
            'shipping_address' => 'json',
        ];
    }

    public function orderDetails(){
        return $this->hasMany(OrderDetail::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function address(){
        return $this->belongsTo(Address::class);
    }

    public function scopeFilterBy($query, $filters){
        return $query->when($filters['status']??false, function ($query, $status) {
            $query->where('status', $status);
        })
        ->when($filters['search']??false, function ($query, $search) {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        });
    }
}
