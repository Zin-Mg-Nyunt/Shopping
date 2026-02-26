<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'total_items',
        'address',
        'payment_method',
        'total_amount',
    ];
    protected $casts = [
        'address' => 'json',
    ];
    public function orderItems(){
        return $this->hasMany(OrderItem::class);
    }

    public function generateOrderNumber(){
        $prefix = 'ORD';
        $date = now()->format('Ymd');
        $random = strtoupper(bin2hex(random_bytes(2)));

        $orderNumber = "{$prefix}-{$date}-{$random}";

        if (Order::where('order_number', $orderNumber)->exists()) {
            return $this->generateOrderNumber();
        }

        return $orderNumber;
    }
}
