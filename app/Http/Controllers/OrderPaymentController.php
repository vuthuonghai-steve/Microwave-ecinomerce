<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderPaymentService;
use App\Services\VNPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class OrderPaymentController extends Controller
{
    public function __construct(private readonly OrderPaymentService $payments, private readonly VNPayService $vnPay)
    {
        $this->middleware('auth')->only(['showForm', 'pay']);
    }

    public function showForm(Request $request, int $orderId)
    {
        $order = Order::where('user_id', $request->user()->id)
            ->with(['items', 'shippingAddress'])
            ->findOrFail($orderId);

        if ($order->status !== 'pending' || $order->payment_status !== 'unpaid') {
            return redirect()
                ->route('orders.show', $orderId)
                ->withErrors(['payment' => 'Don hang chua san sang thanh toan online.']);
        }

        return view('payments.vnpay-checkout', [
            'order' => $order,
            'user' => $request->user(),
        ]);
    }

    public function pay(Request $request, int $orderId)
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'card_number' => ['required', 'digits_between:12,19'],
            'card_expiry_month' => ['required', 'integer', 'between:1,12'],
            'card_expiry_year' => ['required', 'integer', 'between:2000,2100'],
            'card_cvv' => ['required', 'digits_between:3,6'],
        ]);

        $order = Order::where('user_id', $request->user()->id)
            ->with(['payment'])
            ->findOrFail($orderId);

        $customer = Arr::only($validated, ['customer_name', 'customer_email', 'customer_phone']);
        $card = [
            'number_masked' => $this->maskCardNumber($validated['card_number']),
            'expiry' => sprintf('%02d/%s', (int) $validated['card_expiry_month'], (string) $validated['card_expiry_year']),
        ];

        try {
            $payUrl = $this->payments->initiateVNPay($order, (string) $request->ip(), $customer, $card);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (RuntimeException $e) {
            Log::error('VNPay init error web: '.$e->getMessage(), ['order_id' => $order->id]);
            return back()->withErrors(['payment' => 'Thieu cau hinh VNPay. Lien he quan tri vien.']);
        }

        return redirect()->away($payUrl);
    }

    public function handleReturn(Request $request)
    {
        $payload = $request->all();
        $signatureValid = $this->vnPay->verifySignature($payload);
        $isSuccess = ($payload['vnp_ResponseCode'] ?? null) === '00'
            && ($payload['vnp_TransactionStatus'] ?? null) === '00';

        $message = $signatureValid && $isSuccess
            ? 'Thanh toan thanh cong. Don hang se duoc cap nhat khi nhan duoc IPN tu VNPay.'
            : 'Thanh toan chua thanh cong. Vui long kiem tra lai hoac thu lai.';

        return view('payments.vnpay-return', [
            'payload' => $payload,
            'signatureValid' => $signatureValid,
            'isSuccess' => $isSuccess,
            'message' => $message,
        ]);
    }

    protected function maskCardNumber(string $number): string
    {
        $clean = preg_replace('/\D+/', '', $number) ?? '';
        $last4 = substr($clean, -4);
        return str_repeat('*', max(0, strlen($clean) - 4)).$last4;
    }
}
