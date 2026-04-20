<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Promo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function list(Request $request)
    {
        if($request->user()->role === 'admin'){
            return inertia('Admin/OrderList', [
                'orders' => Order::with('orderDetails')->paginate(10)->withQueryString(),
            ]);
        } else {
            $statusCount = $request->user()->orders()->selectRaw("
                count(*) as total,
                count(case when status = 'pending' then 1 end) as pending,
                count(case when status = 'processing' then 1 end) as processing,
                count(case when status = 'shipped' then 1 end) as shipped,
                count(case when status = 'delivered' then 1 end) as delivered,
                count(case when status = 'cancelled' then 1 end) as cancelled
            ")->first();
            return inertia('User/OrderList', [
                'statusCount' => $statusCount,
                'orders' => $request->user()
                            ->orders()
                            ->with('orderDetails')
                            ->when($request->status, function($query) use ($request){
                                $query->where('status', $request->status);
                            })
                            ->latest()
                            ->paginate(3)
                            ->withQueryString(),
            ]);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'address_id' => 'nullable|integer|exists:addresses,id',
            'shipping_address' => 'nullable|array',
            'payment_method' => 'required|string',
            'total_amount' => 'required|numeric',
            'product_ids' => 'required|array',
            'product_ids.*' => 'required|integer|exists:products,id',
            'quantity' => 'required|array',
            'quantity.*' => 'required|integer|min:1',
            'promo_code' => 'nullable|string|exists:promos,code',
        ]);

        try {
            DB::transaction(function() use ($request){
                $order=Order::create([
                    'user_id'=>$request->user()->id,
                    'order_number' => '#ORD-'.now()->format('YmdHis'),
                    'address_id'=>$request->address_id,
                    'shipping_address'=>$request->shipping_address,
                    'payment_method'=>$request->payment_method,
                    'total_quantity'=>0,
                    'total_amount'=>0,
                    'promo_code'=>$request->promo_code,
                ]);
    
                $sub_total=0;
    
                $products = Product::whereIn('id',$request->product_ids)->get();
    
                $quantities = array_combine($request->product_ids, $request->quantity);
                foreach($products as $product){
                    $price = $product->discount_price??$product->price;
                    $quantity = $quantities[$product->id];
                    $sub_total += $price * $quantity;

                    if($product->stock < $quantity){
                        throw new \Exception($product->title . ' is only ' . $product->stock . ' in stock');
                    }
                    
                    $product->decrement('stock', $quantity);

                    $order->orderDetails()->create([
                        'product_id'=>$product->id,
                        'product_image'=>$product->thumbnail,
                        'product_name'=>$product->title,
                        'quantity' => $quantity,
                        'price' => $price,
                        'sub_total' => $price * $quantity,
                    ]);
                }
                
                $tax = $sub_total * 0.05;
                $shipping_fees = $sub_total >= 200 ? 0 : 9.99;
                
                $discount_amount = 0;
                if($request->promo_code){
                    $promo = Promo::where('code',$request->promo_code)->first();
                    if($promo->is_valid){
                        if($promo->discount_type === 'percentage'){
                            $discount_amount = $sub_total * $promo->discount / 100;
                        }else if($promo->discount_type === 'fixed' && $sub_total > 5 * $promo->discount){
                            $discount_amount = $promo->discount;
                        }
                    }
                }
                $total_amount = $sub_total - $discount_amount + $tax + $shipping_fees;
    
                $order->update([
                    'total_quantity'=> array_sum($request->quantity),
                    'total_amount' => $total_amount,
                ]);
    
                $request->user()->carts()->forceDelete();
            });
            return redirect()->back()->with('success', 'Order created successfully');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
