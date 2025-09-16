<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Carrier;
use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user:id,name,email'])
            ->withCount('items')
            ->withSum('items as total_qty','quantity')
            ->latest()
            ->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(int $id)
    {
        $order = Order::with(['user:id,name,email', 'items.product', 'shippingAddress', 'items'])
            ->findOrFail($id);
        $all = ['processing','packed','shipping','delivered','cancelled'];
        $allowedStatuses = array_values(array_filter($all, fn ($s) => $this->canTransition($order->status, $s)));
        $carriers = [
            ['code' => 'ghn', 'name' => 'GHN'],
            ['code' => 'ghtk', 'name' => 'GHTK'],
        ];
        $shipment = \App\Models\Shipment::where('order_id', $order->id)->first();
        return view('admin.orders.show', compact('order','allowedStatuses','carriers','shipment'));
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
                return back()->withErrors(['status' => 'Invalid status transition']);
            }
            $order->status = $data['status'];
        }
        if (isset($data['payment_status'])) {
            if ($order->status !== 'delivered' && ($data['status'] ?? null) !== 'delivered') {
                return back()->withErrors(['payment_status' => 'Can mark paid only when delivered']);
            }
            $order->payment_status = $data['payment_status'];
        }
        $order->save();

        return back()->with('status', 'Order updated');
    }

    public function pushToShipping(Request $request, int $id)
    {
        $order = Order::findOrFail($id);
        $data = $request->validate([
            'carrier_code' => ['required', Rule::in(['ghn','ghtk'])],
        ]);

        $carrier = Carrier::firstOrCreate(
            ['code' => $data['carrier_code']],
            ['name' => strtoupper($data['carrier_code']), 'config' => json_encode([]), 'is_active' => true]
        );

        $shipment = Shipment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'carrier_id' => $carrier->id,
                'tracking_code' => 'TRACK-'.time().'-'.$order->id,
                'status' => 'requested',
                'fee' => null,
                'label_url' => null,
                'raw_payload' => null,
            ]
        );

        if ($order->status === 'pending') {
            $order->status = 'processing';
            $order->save();
        }

        return back()->with('status', 'Pushed to shipping')->with('shipment_id', $shipment->id);
    }
}
