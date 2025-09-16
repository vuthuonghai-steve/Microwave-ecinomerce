<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class OrderPaymentWebTest extends TestCase
{
    use RefreshDatabase;

    protected function createOrder(User $user): Order
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

        return Order::create([
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
        ]);
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

    public function test_user_can_view_vnpay_checkout_form(): void
    {
        $user = User::factory()->create();
        $order = $this->createOrder($user);

        $this->actingAs($user);

        $response = $this->get("/my/orders/{$order->id}/pay");
        $response->assertStatus(200)->assertSee('Thong tin the (sandbox)');
    }

    public function test_form_validation_requires_customer_fields(): void
    {
        $user = User::factory()->create();
        $order = $this->createOrder($user);

        $this->actingAs($user);

        $response = $this->post("/my/orders/{$order->id}/pay", []);
        $response->assertSessionHasErrors([
            'customer_name',
            'customer_email',
            'customer_phone',
            'card_number',
            'card_expiry_month',
            'card_expiry_year',
            'card_cvv',
        ]);
    }

    public function test_successful_submission_redirects_to_vnpay(): void
    {
        $this->ensureConfig();

        $user = User::factory()->create();
        $order = $this->createOrder($user);

        $this->actingAs($user);

        $response = $this->post("/my/orders/{$order->id}/pay", [
            'customer_name' => 'Nguyen Van A',
            'customer_email' => 'customer@example.com',
            'customer_phone' => '0900000000',
            'card_number' => '9704198526191432198',
            'card_expiry_month' => 7,
            'card_expiry_year' => 2015,
            'card_cvv' => '123456',
        ]);

        $response->assertRedirect();
        $this->assertStringContainsString(
            'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html',
            $response->headers->get('Location')
        );
    }
}
