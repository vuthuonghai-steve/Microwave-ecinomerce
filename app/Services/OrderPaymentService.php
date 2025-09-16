<?php

namespace App\Services;

use App\Events\OrderPaid;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class OrderPaymentService
{
    public function __construct(private readonly VNPayService $vnPay)
    {
    }

    public function initiateVNPay(Order $order, string $clientIp, array $customer = [], array $card = []): string
    {
        if ($order->status !== 'pending' || $order->payment_status !== 'unpaid') {
            throw ValidationException::withMessages([
                'order' => 'Don hang khong du dieu kien thanh toan online.',
            ]);
        }

        return DB::transaction(function () use ($order, $clientIp, $customer, $card) {
            $payment = $order->payment;
            if (!$payment) {
                $payment = new Payment([
                    'provider' => 'vnpay',
                    'amount' => $order->grand_total,
                    'status' => 'initiated',
                    'txn_code' => null,
                    'paid_at' => null,
                ]);
                $order->payment()->save($payment);
            }

            $payment->fill([
                'provider' => 'vnpay',
                'amount' => $order->grand_total,
                'status' => 'initiated',
                'txn_code' => null,
            ])->save();

            $transaction = $payment->transactions()->create([
                'gateway' => 'vnpay',
                'txn_ref' => $order->code,
                'amount' => $order->grand_total,
                'status' => 'initiated',
                'response_code' => null,
                'message' => null,
                'raw_request' => null,
            ]);

            [$url, $payload, $secureHash] = $this->vnPay->generatePaymentUrl($order, $transaction, $clientIp, $customer, $card);

            $transaction->update([
                'status' => 'redirected',
                'raw_request' => array_merge($payload, [
                    'vnp_SecureHash' => $secureHash,
                    'customer' => $customer,
                    'card' => $card,
                ]),
            ]);

            $order->update([
                'payment_method' => 'vnpay',
            ]);

            return $url;
        });
    }

    public function handleVNPayIpn(array $payload): array
    {
        if (!$this->vnPay->verifySignature($payload)) {
            Log::warning('VNPay IPN signature invalid', ['payload' => $payload]);
            return ['response' => $this->vnPay->response('97', 'Invalid signature'), 'success' => false];
        }

        $txnRef = $payload['vnp_TxnRef'] ?? null;
        if (!$txnRef) {
            return ['response' => $this->vnPay->response('01', 'Missing order reference'), 'success' => false];
        }

        $order = Order::with(['payment.transactions'])->where('code', $txnRef)->first();
        if (!$order) {
            return ['response' => $this->vnPay->response('01', 'Order not found'), 'success' => false];
        }

        if ($order->payment_status === 'paid') {
            return ['response' => $this->vnPay->response('02', 'Order already paid'), 'success' => true];
        }

        $amount = $this->vnPay->normalizedAmount($payload['vnp_Amount'] ?? '0');
        if (abs($amount - (float) $order->grand_total) > 0.01) {
            Log::warning('VNPay IPN amount mismatch', ['order_id' => $order->id, 'payload' => $payload]);
            return ['response' => $this->vnPay->response('04', 'Invalid amount'), 'success' => false];
        }

        $payment = $order->payment;
        if (!$payment) {
            $payment = new Payment([
                'provider' => 'vnpay',
                'amount' => $order->grand_total,
                'status' => 'initiated',
            ]);
            $order->payment()->save($payment);
        }

        $transaction = $payment->transactions()->where('txn_ref', $txnRef)->latest()->first();
        if (!$transaction) {
            $transaction = $payment->transactions()->create([
                'gateway' => 'vnpay',
                'txn_ref' => $txnRef,
                'amount' => $order->grand_total,
                'status' => 'initiated',
            ]);
        }

        $isSuccess = ($payload['vnp_ResponseCode'] ?? null) === '00'
            && ($payload['vnp_TransactionStatus'] ?? null) === '00';

        $now = now();

        DB::transaction(function () use ($order, $payment, $transaction, $payload, $isSuccess, $amount, $now) {
            $transaction->update([
                'status' => $isSuccess ? 'succeeded' : 'failed',
                'response_code' => $payload['vnp_ResponseCode'] ?? null,
                'message' => $payload['vnp_Message'] ?? null,
                'raw_response' => $payload,
            ]);

            $payment->update([
                'provider' => 'vnpay',
                'amount' => $amount,
                'status' => $isSuccess ? 'succeeded' : 'failed',
                'txn_code' => $payload['vnp_TransactionNo'] ?? $payment->txn_code,
                'paid_at' => $isSuccess ? $now : $payment->paid_at,
            ]);

            if ($isSuccess) {
                $order->update([
                    'payment_status' => 'paid',
                    'payment_method' => 'vnpay',
                    'paid_at' => $now,
                ]);
            }
        });

        if ($isSuccess) {
            event(new OrderPaid($order->refresh()));
            return ['response' => $this->vnPay->response('00', 'Confirm Success'), 'success' => true];
        }

        Log::info('VNPay IPN reported failure', ['order_id' => $order->id, 'payload' => $payload]);
        return ['response' => $this->vnPay->response('00', 'Payment Failed'), 'success' => false];
    }
}
