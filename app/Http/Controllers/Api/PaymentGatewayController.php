<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use RuntimeException;

class PaymentGatewayController extends Controller
{
    public function __construct(private readonly OrderPaymentService $payments)
    {
    }

    public function initiate(Request $request, int $orderId)
    {
        $validated = $request->validate([
            'gateway' => ['required', Rule::in(['vnpay'])],
            'customer_name' => ['sometimes', 'string', 'max:255'],
            'customer_email' => ['sometimes', 'email', 'max:255'],
            'customer_phone' => ['sometimes', 'string', 'max:30'],
            'card_number' => ['sometimes', 'digits_between:12,19'],
            'card_expiry_month' => ['sometimes', 'integer', 'between:1,12'],
            'card_expiry_year' => ['sometimes', 'integer', 'between:2000,2100'],
            'card_cvv' => ['sometimes', 'digits_between:3,6'],
        ]);

        $order = Order::with(['payment.transactions'])
            ->where('id', $orderId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $customer = Arr::only($validated, ['customer_name', 'customer_email', 'customer_phone']);
        $card = [];
        if (!empty($validated['card_number'])) {
            $clean = preg_replace('/\D+/', '', $validated['card_number']);
            $card['number_masked'] = str_repeat('*', max(0, strlen($clean) - 4)).substr($clean, -4);
        }
        if (!empty($validated['card_expiry_month']) && !empty($validated['card_expiry_year'])) {
            $card['expiry'] = sprintf('%02d/%s', (int) $validated['card_expiry_month'], (string) $validated['card_expiry_year']);
        }

        try {
            $payUrl = $this->payments->initiateVNPay($order, (string) $request->ip(), array_filter($customer), $card);
        } catch (RuntimeException $e) {
            Log::error('VNPay init error: '.$e->getMessage(), ['order_id' => $order->id]);
            return response()->json([
                'message' => 'Thieu cau hinh VNPay. Lien he quan tri vien.',
            ], 500);
        }

        return response()->json([
            'payUrl' => $payUrl,
        ]);
    }

    public function ipn(Request $request)
    {
        $result = $this->payments->handleVNPayIpn($request->all());
        return response()->json($result['response']);
    }
}
