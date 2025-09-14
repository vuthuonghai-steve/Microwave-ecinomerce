<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\ProductStock>
 */
class ProductStockFactory extends Factory
{
    protected $model = ProductStock::class;

    public function definition(): array
    {
        $onHand = $this->faker->numberBetween(0, 200);
        $reserved = $onHand > 0 ? $this->faker->numberBetween(0, min(20, $onHand)) : 0;

        return [
            'product_id' => Product::inRandomOrder()->value('id') ?? Product::factory(),
            'stock_on_hand' => $onHand,
            'stock_reserved' => $reserved,
        ];
    }
}

