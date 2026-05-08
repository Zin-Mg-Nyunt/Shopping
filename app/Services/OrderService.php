<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\Promo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    public function getOrders(?Request $request)
    {
        $query = $request?->user() && $request->user()->isAdmin()
                    ? Order::query()->with(['user:id,name,email', 'orderDetails'])
                    : $request->user()->orders()->with('orderDetails');
        $limit = $request?->user()->isAdmin() ? 10 : 3;

        return $query->filterBy($request->all())
            ->latest()
            ->paginate($limit)
            ->withQueryString();
    }

    public function getStatusCount(?Request $request)
    {
        $query = $request?->user() && $request->user()->isAdmin()
                    ? Order::query()
                    : $request->user()->orders();

        return $query->selectRaw("
                count(*) as `all`,
                count(case when status = 'pending' then 1 end) as pending,
                count(case when status = 'processing' then 1 end) as processing,
                count(case when status = 'shipped' then 1 end) as shipped,
                count(case when status = 'delivered' then 1 end) as delivered,
                count(case when status = 'cancelled' then 1 end) as cancelled
        ")->first();
    }

    public function store(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $user = $request->user();
                $order = Order::create([
                    'user_id' => $user->id,
                    'order_number' => '#ORD-'.now()->format('YmdHis'),
                    'address_id' => $request->address_id,
                    'shipping_address' => $request->shipping_address,
                    'payment_method' => $request->payment_method,
                    'total_quantity' => 0,
                    'total_amount' => 0,
                    'tax' => 0,
                    'shipping_fees' => 0,
                    'promo_code' => $request->promo_code,
                    'points_used' => 0,
                ]);

                $sub_total = 0;

                $products = Product::whereIn('id', $request->product_ids)->get();

                $quantities = array_combine($request->product_ids, $request->quantity);
                foreach ($products as $product) {
                    $price = $product->discount_price ?? $product->price;
                    $quantity = $quantities[$product->id];
                    $sub_total += $price * $quantity;

                    if ($product->stock < $quantity) {
                        throw new \Exception($product->title.' is only '.$product->stock.' in stock');
                    }

                    $product->decrement('stock', $quantity);

                    $order->orderDetails()->create([
                        'product_id' => $product->id,
                        'product_image' => $product->thumbnail,
                        'product_name' => $product->title,
                        'quantity' => $quantity,
                        'price' => $price,
                        'sub_total' => $price * $quantity,
                    ]);
                }

                $tax = $sub_total * 0.05;
                $shipping_fees = $sub_total >= 200 ? 0 : 9.99;

                $discount_amount = 0;
                if ($request->promo_code) {
                    $promo = Promo::where('code', $request->promo_code)->first();
                    if ($promo->is_valid) {
                        if ($promo->discount_type === 'percentage') {
                            $discount_amount = $sub_total * $promo->discount / 100;
                        } elseif ($promo->discount_type === 'fixed' && $sub_total > 5 * $promo->discount) {
                            $discount_amount = $promo->discount;
                        }
                    }
                }
                $total_amount = $sub_total - $discount_amount + $tax + $shipping_fees;

                $pointsUsed = 0;
                if ($request->use_points && $user->points > 0) {
                    $pointsUsed = min($user->points, $total_amount);
                    $total_amount -= $pointsUsed;
                    $user->decrement('points', $pointsUsed);
                }

                $order->update([
                    'total_quantity' => array_sum($request->quantity),
                    'total_amount' => $total_amount,
                    'points_used' => $pointsUsed,
                    'tax' => $tax,
                    'shipping_fees' => $shipping_fees,
                ]);

                $user->carts()->forceDelete();
            });

            return redirect()->back()->with('success', 'Order created successfully');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function completeOrder(Order $order)
    {
        $order->user()->increment('cached_orders_count');
        if ($order->user->cached_orders_count === 100) {
            Log::info('User is a loyalty customer', ['user_id' => $order->user->id]);
        }
    }
}
