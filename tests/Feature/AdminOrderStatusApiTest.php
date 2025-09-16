<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminOrderStatusApiTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        $admin = User::factory()->create(['email' => 'admin@example.com']);
        \DB::table('users')->where('id', $admin->id)->update(['role' => 'ADMIN']);
        return $admin->fresh();
    }

    private function makeOrder(User $customer, string $status = 'pending'): Order
    {
        $addr = Address::create([
            'user_id' => $customer->id,
            'full_name' => 'John Doe',
            'phone' => '0900000000',
            'line1' => '123 Street',
            'district' => 'District 1',
            'city' => 'HCMC',
            'country_code' => 'VN',
            'is_default' => true,
        ]);

        return Order::create([
            'user_id' => $customer->id,
            'code' => 'ORD-'.time(),
            'status' => $status,
            'payment_status' => 'unpaid',
            'payment_method' => 'cod',
            'subtotal' => 100000,
            'discount_total' => 0,
            'shipping_fee' => 0,
            'grand_total' => 100000,
            'shipping_address_id' => $addr->id,
            'notes' => null,
        ]);
    }

    public function test_valid_status_transitions_pass(): void
    {
        $admin = $this->makeAdmin();
        Sanctum::actingAs($admin);
        $customer = User::factory()->create();
        $order = $this->makeOrder($customer, 'pending');

        // pending -> processing
        $this->patchJson("/api/admin/orders/{$order->id}", ['status' => 'processing'])
            ->assertOk()
            ->assertJsonPath('status', 'processing');

        // processing -> packed
        $this->patchJson("/api/admin/orders/{$order->id}", ['status' => 'packed'])
            ->assertOk()
            ->assertJsonPath('status', 'packed');

        // packed -> shipping
        $this->patchJson("/api/admin/orders/{$order->id}", ['status' => 'shipping'])
            ->assertOk()
            ->assertJsonPath('status', 'shipping');

        // shipping -> delivered
        $this->patchJson("/api/admin/orders/{$order->id}", ['status' => 'delivered'])
            ->assertOk()
            ->assertJsonPath('status', 'delivered');

        // can mark paid when delivered
        $this->patchJson("/api/admin/orders/{$order->id}", ['payment_status' => 'paid'])
            ->assertOk()
            ->assertJsonPath('payment_status', 'paid');
    }

    public function test_invalid_transition_is_blocked(): void
    {
        $admin = $this->makeAdmin();
        Sanctum::actingAs($admin);
        $customer = User::factory()->create();
        $order = $this->makeOrder($customer, 'pending');

        // invalid: pending -> shipping
        $this->patchJson("/api/admin/orders/{$order->id}", ['status' => 'shipping'])
            ->assertStatus(400)
            ->assertJson(['message' => 'Invalid status transition']);
    }

    public function test_cannot_mark_paid_before_delivered(): void
    {
        $admin = $this->makeAdmin();
        Sanctum::actingAs($admin);
        $customer = User::factory()->create();
        $order = $this->makeOrder($customer, 'processing');

        $this->patchJson("/api/admin/orders/{$order->id}", ['payment_status' => 'paid'])
            ->assertStatus(400)
            ->assertJson(['message' => 'Payment can be marked paid only when delivered']);
    }
}

