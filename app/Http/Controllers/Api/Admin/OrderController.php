<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $q = Order::query()->with(['user:id,name,email', 'items', 'shippingAddress'])
            ->withCount('items')
            ->withSum('items as total_qty','quantity');
        if ($status = $request->string('status')->toString()) {
            $q->where('status', $status);
        }
        if ($userId = $request->integer('user_id')) {
            $q->where('user_id', $userId);
        }
        $orders = $q->latest()->paginate(20)->appends($request->query());
        return response()->json($orders);
    }

    public function show(int $id)
    {
        $order = Order::with(['user:id,name,email', 'items', 'shippingAddress'])->findOrFail($id);
        return response()->json($order);
    }

    private function canTransition(string $from, string $to): bool
    {
        $map = [
            'pending' => ['processing','cancelled'],
            'processing' => ['packed','cancelled'],
            'packed' => ['shipping','cancelled'],
            'shipping' => ['delivered','cancelled'],
            'delivered' => [],
            'cancelled' => [],
        ];
        return in_array($to, $map[$from] ?? [], true);
    }

    public function updateStatus(Request $request, int $id)
    {
        $order = Order::findOrFail($id);

        $data = $request->validate([
            'status' => ['nullable', Rule::in(['processing','packed','shipping','delivered','cancelled'])],
            'payment_status' => ['nullable', Rule::in(['paid'])],
        ]);

        if (isset($data['status'])) {
            if (!$this->canTransition($order->status, $data['status'])) {
                return response()->json(['message' => 'Invalid status transition'], 400);
            }
            $order->status = $data['status'];
        }
        if (isset($data['payment_status'])) {
            // Allow marking paid only when delivered (COD confirmation)
            if ($order->status !== 'delivered' && ($data['status'] ?? null) !== 'delivered') {
                return response()->json(['message' => 'Payment can be marked paid only when delivered'], 400);
            }
            $order->payment_status = $data['payment_status'];
        }
        $order->save();

        return response()->json($order->fresh());
    }
}
