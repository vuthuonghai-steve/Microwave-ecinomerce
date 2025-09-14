<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ReportsDemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create(['email' => 'demo@example.com']);
        }

        $address = Address::firstOrCreate(
            ['user_id' => $user->id, 'line1' => '123 Demo St', 'district' => 'District 1', 'city' => 'HCM'],
            ['full_name' => $user->name, 'phone' => '0900000000', 'country_code' => 'VN', 'is_default' => true]
        );

        $products = Product::where('is_active', true)->inRandomOrder()->take(20)->get();
        if ($products->isEmpty()) {
            // Ensure at least some products exist from catalog seeder
            $this->call(CatalogSeeder::class);
            $products = Product::where('is_active', true)->inRandomOrder()->take(20)->get();
        }

        $hotProducts = $products->take(5)->pluck('id')->all();

        // Seed orders over last 90 days with bias to hot products
        $days = 90;
        for ($i = $days; $i >= 0; $i--) {
            $orderCount = random_int(0, 3); // 0..3 orders per day
            for ($j = 0; $j < $orderCount; $j++) {
                $created = now()->copy()->subDays($i)->setTime(random_int(8, 20), random_int(0, 59));

                $order = Order::create([
                    'user_id' => $user->id,
                    'code' => 'ORD-DEM-'.Str::upper(Str::random(6)),
                    'status' => 'delivered',
                    'payment_status' => 'paid',
                    'payment_method' => 'cod',
                    'subtotal' => 0,
                    'discount_total' => 0,
                    'shipping_fee' => 30000,
                    'grand_total' => 0,
                    'shipping_address_id' => $address->id,
                    'notes' => null,
                    'created_at' => $created,
                    'updated_at' => $created,
                ]);

                $itemsInOrder = random_int(1, 4);
                $subtotal = 0;
                for ($k = 0; $k < $itemsInOrder; $k++) {
                    // 60% chance pick hot product, else random from pool
                    if (!empty($hotProducts) && random_int(1, 100) <= 60) {
                        $pid = $hotProducts[array_rand($hotProducts)];
                        $product = $products->firstWhere('id', $pid) ?? $products->random();
                    } else {
                        $product = $products->random();
                    }

                    $price = (float) ($product->sale_price ?? $product->price);
                    $qty = random_int(1, 3);
                    $lineTotal = $price * $qty;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'name_snapshot' => $product->name,
                        'price_snapshot' => $price,
                        'quantity' => $qty,
                        'total' => $lineTotal,
                        'created_at' => $created,
                        'updated_at' => $created,
                    ]);

                    $subtotal += $lineTotal;
                }

                $order->update([
                    'subtotal' => $subtotal,
                    'grand_total' => $subtotal + 30000,
                ]);
            }
        }
    }
}

