<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Services\CheckoutService;
use App\Services\VNPayService;
use Illuminate\Http\Request;

class CheckoutWebController extends Controller
{
    public function __construct(private readonly CheckoutService $service)
    {
        $this->middleware('auth');
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $cart = Cart::firstOrCreate(['user_id' => $user->id, 'status' => 'active'])->load('items.product');
        $addresses = Address::where('user_id', $user->id)->orderByDesc('is_default')->get();
        return view('cart.checkout', compact('cart','addresses'));
    }

     public function store(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'shipping_address_id' => ['required', 'integer', 'exists:addresses,id'],
            'payment_method' => ['required', 'string', 'in:cod,vnpay'],
        ]);

        $cart = Cart::firstOrCreate(['user_id' => $user->id, 'status' => 'active']);
        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->withErrors('Giỏ hàng của bạn đang trống.');
        }

        $order = $this->service->checkout($cart, (int)$validated['shipping_address_id']);

        if ($validated['payment_method'] === 'vnpay') {
            // User wants to pay with VNPay
            $vnpayService = app(VNPayService::class);
            $payment = $order->payment;
            $payment->update(['provider' => 'vnpay']);

            $paymentTransaction = $payment->transactions()->create([
                'gateway' => 'vnpay',
                'amount' => $order->grand_total,
                'txn_ref' => uniqid(),
                'status' => 'initiated',
            ]);

            [$url] = $vnpayService->generatePaymentUrl($order, $paymentTransaction, $request->ip());
            return redirect()->away($url);
        }

        // Default to COD
        return redirect()->route('orders.show', $order->id)->with('status', 'Đặt hàng thành công');
    }
}

