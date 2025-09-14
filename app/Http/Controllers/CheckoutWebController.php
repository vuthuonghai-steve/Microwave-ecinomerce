<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Cart;
use App\Services\CheckoutService;
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
            'shipping_address_id' => ['required','integer','exists:addresses,id']
        ]);
        $cart = Cart::firstOrCreate(['user_id' => $user->id, 'status' => 'active']);
        $order = $this->service->checkout($cart, (int)$validated['shipping_address_id']);
        return redirect()->route('orders.show', $order->id)->with('status','Đặt hàng thành công');
    }
}

