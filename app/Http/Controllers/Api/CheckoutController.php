<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Services\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function __construct(private readonly CheckoutService $service)
    {
    }

    protected function resolveUserId(Request $request): int
    {
        $id = (int) auth()->id();
        if (!$id) {
            throw ValidationException::withMessages(['user' => 'Unauthenticated']);
        }
        return $id;
    }

    public function store(Request $request)
    {
        $userId = $this->resolveUserId($request);
        $validated = $request->validate([
            'shipping_address_id' => ['required','integer','exists:addresses,id']
        ]);

        $cart = Cart::firstOrCreate(['user_id' => $userId, 'status' => 'active']);

        $order = $this->service->checkout($cart, (int)$validated['shipping_address_id']);
        return response()->json([
            'data' => [
                'order_id' => $order->id,
                'code' => $order->code,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'subtotal' => (float)$order->subtotal,
                'discount_total' => (float)$order->discount_total,
                'shipping_fee' => (float)$order->shipping_fee,
                'grand_total' => (float)$order->grand_total,
            ]
        ], 201);
    }
}
