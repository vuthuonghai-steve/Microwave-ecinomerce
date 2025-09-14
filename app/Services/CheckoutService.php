<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ProductStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class CheckoutService
{
    public function checkout(Cart $cart, int $shippingAddressId): Order
    {
        if ($cart->items()->count() === 0) {
            throw ValidationException::withMessages([
                'cart' => 'Cart is empty',
            ]);
        }

        $address = Address::where('user_id', $cart->user_id)->findOrFail($shippingAddressId);

        return DB::transaction(function () use ($cart, $address) {
            $items = $cart->items()->with('product')->get();
            $productIds = $items->pluck('product_id')->all();

            // Pessimistic lock to avoid race conditions
            $stocks = ProductStock::whereIn('product_id', $productIds)->lockForUpdate()->get()->keyBy('product_id');

            $errors = [];
            foreach ($items as $it) {
                $stock = $stocks->get($it->product_id);
                $available = max(0, ($stock->stock_on_hand ?? 0) - ($stock->stock_reserved ?? 0));
                if ($available < $it->quantity) {
                    $errors['product_'.$it->product_id] = 'Insufficient stock';
                }
            }
            if (!empty($errors)) {
                throw ValidationException::withMessages($errors);
            }

            // Totals
            $subtotal = $items->reduce(fn($c, $it) => $c + ($it->price_snapshot * $it->quantity), 0);
            $discount = 0; // extend later with vouchers
            $shippingFee = 30000; // per rules CO-12
            $grand = $subtotal - $discount + $shippingFee;

            // Create order
            $order = Order::create([
                'user_id' => $cart->user_id,
                'code' => $this->generateOrderCode(),
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'payment_method' => 'cod',
                'subtotal' => $subtotal,
                'discount_total' => $discount,
                'shipping_fee' => $shippingFee,
                'grand_total' => $grand,
                'shipping_address_id' => $address->id,
                'notes' => null,
            ]);

            foreach ($items as $it) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $it->product_id,
                    'name_snapshot' => $it->product->name,
                    'price_snapshot' => $it->price_snapshot,
                    'quantity' => $it->quantity,
                    'total' => $it->price_snapshot * $it->quantity,
                ]);

                // Increase reserved per rules CO-10
                $stock = $stocks->get($it->product_id);
                $stock->increment('stock_reserved', $it->quantity);
            }

            // Create payment placeholder
            Payment::create([
                'order_id' => $order->id,
                'provider' => 'cod',
                'amount' => $grand,
                'status' => 'initiated',
                'txn_code' => null,
                'paid_at' => null,
            ]);

            // Convert cart
            $cart->update(['status' => 'converted']);

            return $order->load('items');
        });
    }

    protected function generateOrderCode(): string
    {
        return 'ORD-'.now()->format('Ymd-His').'-'.Str::upper(Str::random(4));
    }
}

