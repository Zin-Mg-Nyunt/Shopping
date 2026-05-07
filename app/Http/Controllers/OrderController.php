<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function adminList(Request $request, OrderService $orderService)
    {
        Gate::authorize('admin');
        return inertia('Admin/OrderList', [
            'orders' => $orderService->getOrders($request),
            'statusCount' => $orderService->getStatusCount($request),
        ]);
    }

    public function userList(Request $request, OrderService $orderService)
    {
        return inertia('User/OrderList', [
            'orders' => $orderService->getOrders($request),
            'statusCount' => $orderService->getStatusCount($request),
        ]);
    }

    public function store(Request $request, OrderService $orderService)
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
            'use_points' => 'nullable|boolean',
        ]);

        return $orderService->store($request);
    }

    public function updateStatus(Request $request, Order $order)
    {
        Gate::authorize('admin');

        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'processing', 'shipped', 'delivered', 'cancelled'])],
        ]);

        $order->update([
            'status' => $validated['status'],
        ]);

        return redirect()->back()->with('success', 'Order status updated successfully.');
    }
}
