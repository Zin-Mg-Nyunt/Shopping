<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class MergeCartAfterLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;
        $getSessionProducts=session()->get('cart',[]);
        if($getSessionProducts){
            collect($getSessionProducts)->each(function($quantity,$id) use ($user){
                $user->cartProducts()->updateOrCreate(
                    ['product_id' => $id],
                    ['quantity' => DB::raw('quantity + '.$quantity)]
                );
            });
        }
        session()->forget('cart');
    }
}
