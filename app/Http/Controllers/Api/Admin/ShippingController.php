<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Carrier;
use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShippingController extends Controller
{
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

        // log event
        $shipment->events()->create([
            'status' => 'requested',
            'message' => 'Pushed to shipping provider '.strtoupper($data['carrier_code']),
            'occurred_at' => now(),
            'raw' => null,
        ]);

        // Optionally update order status to processing
        if ($order->status === 'pending') {
            $order->status = 'processing';
            $order->save();
        }

        return response()->json($shipment->fresh());
    }

    public function webhook(Request $request)
    {
        // Basic stub: update shipment by tracking code and create event
        $payload = $request->all();
        $tracking = data_get($payload, 'tracking_code');
        $status = data_get($payload, 'status', 'unknown');
        $message = data_get($payload, 'message');

        if ($tracking) {
            $shipment = Shipment::where('tracking_code', $tracking)->first();
            if ($shipment) {
                $shipment->update(['status' => $status]);
                $shipment->events()->create([
                    'status' => $status,
                    'message' => $message,
                    'occurred_at' => now(),
                    'raw' => $payload,
                ]);
            }
        }

        return response()->json(['received' => true]);
    }
}
