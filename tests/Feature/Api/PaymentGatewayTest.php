<?php

namespace Tests\Feature\Api;

use App\Events\OrderPaid;
use App\Models\Address;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PaymentGatewayTest extends TestCase
{
    use RefreshDatabase;

    protected function createOrderForUser(User $user, array $overrides = []): Order
    {
        $address = Address::create([
            'user_id' => $user->id,
            'full_name' => 'Test User',
            'phone' => '0123456789',
            'line1' => '123 Test Street',
            'district' => 'District',
            'city' => 'City',
            'country_code' => 'VN',
            'is_default' => true,
        ]);

        return Order::create(array_merge([
            'user_id' => $user->id,
            'code' => 'ORD-'.Str::random(6),
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'cod',
            'subtotal' => 100000,
            'discount_total' => 0,
            'shipping_fee' => 0,
            'grand_total' => 100000,
            'shipping_address_id' => $address->id,
            'notes' => null,
        ], $overrides));
    }

    protected function ensureConfig(): void
    {
        config([
            'vnpay.tmn_code' => 'TESTCODE',
            'vnpay.hash_secret' => 'secret',
            'vnpay.base_url' => 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html',
            'vnpay.return_url' => 'https://example.com/payment/vnpay/return',
        ]);
    }

    public function test_user_can_initiate_vnpay_payment(): void
    {
        $this->ensureConfig();

        $user = User::factory()->create();
        $order = $this->createOrderForUser($user);

        Payment::create([
            'order_id' => $order->id,
            'provider' => 'cod',
            'amount' => $order->grand_total,
            'status' => 'initiated',
            'txn_code' => null,
            'paid_at' => null,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/orders/{$order->id}/pay", ['gateway' => 'vnpay']);

        $response->assertOk()->assertJsonStructure(['payUrl']);

        $order->refresh();
        $this->assertSame('vnpay', $order->payment_method);

        $transaction = PaymentTransaction::where('payment_id', $order->payment->id)->first();
        $this->assertNotNull($transaction);
        $this->assertSame('redirected', $transaction->status);
        $this->assertArrayHasKey('vnp_TxnRef', $transaction->raw_request ?? []);
    }

    public function test_vnpay_ipn_marks_order_paid(): void
    {
        $this->ensureConfig();

        $user = User::factory()->create();
        $order = $this->createOrderForUser($user, ['payment_method' => 'vnpay']);

        $payment = Payment::create([
            'order_id' => $order->id,
            'provider' => 'vnpay',
            'amount' => $order->grand_total,
            'status' => 'initiated',
            'txn_code' => null,
            'paid_at' => null,
        ]);

        $transaction = PaymentTransaction::create([
            'payment_id' => $payment->id,
            'gateway' => 'vnpay',
            'txn_ref' => $order->code,
            'amount' => $order->grand_total,
            'status' => 'redirected',
        ]);

        $payload = [
            'vnp_TxnRef' => $order->code,
            'vnp_Amount' => (string) ((int) ($order->grand_total * 100)),
            'vnp_ResponseCode' => '00',
            'vnp_TransactionStatus' => '00',
            'vnp_TransactionNo' => '12345678',
            'vnp_OrderInfo' => 'Thanh toan don hang '.$order->code,
        ];

        $hashInput = collect($payload)->sortKeys()->map(fn($value, $key) => $key.'='.rawurlencode($value))->implode('&');
        $payload['vnp_SecureHash'] = hash_hmac('sha512', $hashInput, config('vnpay.hash_secret'));

        Event::fake();

        $response = $this->postJson('/api/payment/ipn', $payload);
        $response->assertOk()->assertJson(['RspCode' => '00']);

        $order->refresh();
        $payment->refresh();
        $transaction->refresh();

        $this->assertSame('paid', $order->payment_status);
        $this->assertNotNull($order->paid_at);
        $this->assertSame('succeeded', $payment->status);
        $this->assertSame('12345678', $payment->txn_code);
        $this->assertSame('succeeded', $transaction->status);
        $this->assertEquals($payload, $transaction->raw_response);

        Event::assertDispatched(OrderPaid::class, function ($event) use ($order) {
            return $event->order->id === $order->id;
        });
    }

    public function test_vnpay_ipn_rejects_invalid_signature(): void
    {
        $this->ensureConfig();

        $payload = [
            'vnp_TxnRef' => 'ORD-INVALID',
            'vnp_Amount' => '10000',
            'vnp_ResponseCode' => '00',
            'vnp_TransactionStatus' => '00',
            'vnp_SecureHash' => 'invalid',
        ];

        $response = $this->postJson('/api/payment/ipn', $payload);
        $response->assertOk()->assertJson(['RspCode' => '97']);
    }
}
