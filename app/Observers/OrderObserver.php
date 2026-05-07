<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\OrderService;

class OrderObserver
{
    public function __construct(protected OrderService $orderService)
    {
    }
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        if ($order->isDirty('status') 
            && $order->status === 'delivered' 
            && !$order->is_counted_in_cache)
        {
            $this->orderService->completeOrder($order);
            $order->is_counted_in_cache = true;
            $order->saveQuietly();
        }
        
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        $order->orderDetails()->delete();
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
