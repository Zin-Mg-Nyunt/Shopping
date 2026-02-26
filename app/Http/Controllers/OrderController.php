<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class OrderController extends Controller
{
    /**
     * Example orders for UI demo. Replace with Order::query()->... when DB is ready.
     *
     * @return array<int, array<string, mixed>>
     */
    private function exampleOrders(): array
    {
        $base = now()->subDays(2);

        return [
            [
                'id' => 1,
                'order_number' => 'ORD-5501',
                'customer_name' => 'Alice Johnson',
                'customer_email' => 'alice@example.com',
                'created_at' => $base->copy()->format('Y-m-d\TH:i:s.v\Z'),
                'total' => 149.97,
                'payment_method' => 'kpay',
                'status' => 'delivered',
                'shipping_address' => '123 Main St, Yangon',
                'items' => [
                    ['product_name' => 'Wireless Earbuds Pro', 'quantity' => 1, 'unit_price' => 79.99, 'line_total' => 79.99],
                    ['product_name' => 'Phone Stand', 'quantity' => 2, 'unit_price' => 34.99, 'line_total' => 69.98],
                ],
            ],
            [
                'id' => 2,
                'order_number' => 'ORD-5502',
                'customer_name' => 'Bob Smith',
                'customer_email' => 'bob@example.com',
                'created_at' => $base->copy()->addDay()->format('Y-m-d\TH:i:s.v\Z'),
                'total' => 299.50,
                'payment_method' => 'wave',
                'status' => 'processing',
                'shipping_address' => '456 Oak Ave, Mandalay',
                'items' => [
                    ['product_name' => 'Laptop Sleeve', 'variant' => 'Grey', 'quantity' => 1, 'unit_price' => 45.00, 'line_total' => 45.00],
                    ['product_name' => 'USB-C Hub', 'variant' => '7-in-1', 'quantity' => 1, 'unit_price' => 89.50, 'line_total' => 89.50],
                    ['product_name' => 'Monitor Arm', 'quantity' => 1, 'unit_price' => 165.00, 'line_total' => 165.00],
                ],
            ],
            [
                'id' => 3,
                'order_number' => 'ORD-5503',
                'customer_name' => 'Carol Davis',
                'customer_email' => 'carol@example.com',
                'created_at' => $base->copy()->addDays(2)->format('Y-m-d\TH:i:s.v\Z'),
                'total' => 67.98,
                'payment_method' => 'card',
                'status' => 'pending',
                'shipping_address' => '789 Pine Rd, Naypyidaw',
                'items' => [
                    ['product_name' => 'Screen Cleaner Kit', 'quantity' => 2, 'unit_price' => 19.99, 'line_total' => 39.98],
                    ['product_name' => 'Cable Organizer', 'quantity' => 1, 'unit_price' => 28.00, 'line_total' => 28.00],
                ],
            ],
        ];
    }

    /**
     * Admin orders list page.
     *
     * Backend logic to implement:
     * - Stats: totalRevenue, newOrdersCount, pendingOrdersCount (and optional trend %).
     * - Query orders from $request->only('search', 'status', 'dateFrom', 'dateTo').
     * - Paginate (e.g. Order::query()->...->paginate(15)).
     * - Each order: id, order_number, customer_name, created_at, total, payment_method, status.
     * - Pass to Inertia: orders, filter, stats.
     */
    public function adminOrders(Request $request): Response
    {
        // TODO: Replace with Order::query()->...->paginate(15) and real stats.
        $exampleList = $this->exampleOrders();
        $totalRevenue = array_sum(array_column($exampleList, 'total'));
        $pendingCount = (int) collect($exampleList)->where('status', 'pending')->count();

        return Inertia::render('Admin/Products/orders', [
            'orders' => [
                'data' => $exampleList,
                'total' => count($exampleList),
                'current_page' => 1,
                'last_page' => 1,
                'links' => [],
            ],
            'filter' => [
                'search' => $request->input('search'),
                'status' => $request->input('status'),
                'dateFrom' => $request->input('dateFrom'),
                'dateTo' => $request->input('dateTo'),
            ],
            'stats' => [
                'totalRevenue' => $totalRevenue,
                'newOrdersCount' => count($exampleList),
                'pendingOrdersCount' => $pendingCount,
            ],
        ]);
    }

    /**
     * Admin order detail page.
     *
     * Backend: load Order by $id with items (product relation for thumbnail, variant),
     * activity_log, return 404 if not found. Pass to Inertia: 'order' with:
     * - id, order_number, customer_name, customer_email, customer_phone, created_at,
     * - total, payment_method, status, shipping_address,
     * - bank_slip_url (or bank_slip_path), transaction_id, admin_notes,
     * - items[] (product_name, variant, thumbnail_url, quantity, unit_price, line_total),
     * - activity_log[] (user_name, action, created_at).
     */
    public function show(Request $request, int $id): Response|HttpResponse
    {
        // TODO: $order = Order::with('items.product')->findOrFail($id); transform for Inertia.
        $orders = $this->exampleOrders();
        $order = collect($orders)->firstWhere('id', $id);
        if (! $order) {
            abort(404);
        }

        $order['customer_phone'] = $order['customer_phone'] ?? '+95 9 123 456 789';
        $order['transaction_id'] = $order['transaction_id'] ?? 'TXN-'.str_pad((string) $id, 6, '0', STR_PAD_LEFT);
        $order['admin_notes'] = $order['admin_notes'] ?? '';
        $order['activity_log'] = [
            ['user_name' => 'Admin User', 'action' => 'Status set to '.ucfirst($order['status']), 'created_at' => $order['created_at']],
        ];
        // Optional: order.bank_slip_url for payment verification thumbnail (expand on click).

        return Inertia::render('Admin/Products/order_detail', [
            'order' => $order,
        ]);
    }

    /**
     * User orders list page. Pass orders for card layout; filter by status on frontend or via query.
     */
    public function userOrders(Request $request): Response
    {
        $orders = $this->userOrdersList();

        return Inertia::render('User/Orders', [
            'orders' => $orders,
        ]);
    }

    /**
     * Orders list for user UI. Each: id, order_number, created_at, status, total, items (name, image_url).
     *
     * @return array<int, array<string, mixed>>
     */
    private function userOrdersList(): array
    {
        $base = now();

        return [
            [
                'id' => 1,
                'order_number' => 'ORD-7721',
                'created_at' => $base->copy()->subDays(2)->format('Y-m-d\TH:i:s.v\Z'),
                'status' => 'shipped',
                'total' => 268.62,
                'items' => [
                    ['name' => 'Wireless Earbuds Pro', 'image_url' => null],
                    ['name' => 'Phone Stand', 'image_url' => null],
                    ['name' => 'USB-C Hub 7-in-1', 'image_url' => null],
                ],
            ],
            [
                'id' => 2,
                'order_number' => 'ORD-7722',
                'created_at' => $base->copy()->subDays(5)->format('Y-m-d\TH:i:s.v\Z'),
                'status' => 'delivered',
                'total' => 239.76,
                'items' => [
                    ['name' => 'Laptop Sleeve', 'image_url' => null],
                    ['name' => 'Monitor Arm', 'image_url' => null],
                ],
            ],
            [
                'id' => 3,
                'order_number' => 'ORD-7723',
                'created_at' => $base->copy()->subDay()->format('Y-m-d\TH:i:s.v\Z'),
                'status' => 'processing',
                'total' => 49.65,
                'items' => [
                    ['name' => 'Screen Cleaner Kit', 'image_url' => null],
                ],
            ],
            [
                'id' => 4,
                'order_number' => 'ORD-7724',
                'created_at' => $base->copy()->subDays(10)->format('Y-m-d\TH:i:s.v\Z'),
                'status' => 'cancelled',
                'total' => 89.50,
                'items' => [
                    ['name' => 'USB-C Hub', 'image_url' => null],
                ],
            ],
        ];
    }

    /**
     * User order detail page. UI demo with sample orders; replace with Order::findOrFail($id) when ready.
     */
    public function userOrderDetail(Request $request, int $id): Response|HttpResponse
    {
        $samples = $this->userOrdersList();
        $order = collect($samples)->firstWhere('id', $id);
        if (! $order) {
            abort(404);
        }

        return Inertia::render('User/OrderDetail', [
            'order' => $order,
        ]);
    }

    public function store(Request $request){
        $request->validate([
            'address' => 'required|array',
            'payment_method' => 'required|string',
            'items' => 'required|array',
        ]);
        
        $totalItems = collect($request->items)->sum('quantity');
        $orderNumber = (new Order)->generateOrderNumber();

        try{
            DB::transaction(function() use ($request, $totalItems, $orderNumber) {
                $order = Order::create([
                    'user_id' => $request->user()->id,
                    'total_amount' => 0,
                    'total_items' => $totalItems,
                    'address' => $request->address,
                    'payment_method' => $request->payment_method,
                    'order_number' => $orderNumber,
                ]);

                $totalAmount = 0;
                $shippingFee = 0;

                foreach ($request->items as $item) {
                    $quantity = $item['quantity'];
                    $product = Product::find($item['product_id']);

                    if($product->stock == 0) throw new \Exception('Product '.$product->name.' is out of stock');
                    if($product->stock < $quantity) throw new \Exception('Product '.$product->name.' stock is not enough');

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_name' => $product->name,
                        'product_price' => $product->price,
                        'quantity' => $quantity,
                    ]);

                    $product->decrement('stock', $quantity);
                    $totalAmount += $product->price * $quantity;
                }

                $shippingFee = $totalAmount <= 100 ? 9.99 : 0;
                $order->update([
                    'total_amount' => $totalAmount + $shippingFee,
                ]);
                return redirect()->back()->with('success', 'Order created successfully');
            });
        }catch(\Exception $e){
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
