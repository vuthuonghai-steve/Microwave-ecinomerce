<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $base = $this->faker->randomElement([
            'Lò vi sóng', 'Microwave', 'Lò vi sóng cao cấp', 'Lò vi sóng đa năng'
        ]);
        $capacity = $this->faker->numberBetween(17, 35);
        $name = $base . ' ' . $capacity . 'L ' . $this->faker->bothify('Model-###');
        $price = $this->faker->numberBetween(1000000, 8000000);
        $hasSale = $this->faker->boolean(40);
        $sale = $hasSale ? round($price * $this->faker->randomFloat(2, 0.7, 0.95), -3) : null;

        return [
            'category_id' => Category::inRandomOrder()->value('id') ?? Category::factory(),
            'brand_id' => Brand::inRandomOrder()->value('id') ?? Brand::factory(),
            'name' => $name,
            'slug' => Str::slug($name . '-' . Str::random(4)),
            'price' => $price,
            'sale_price' => $sale,
            'capacity_liters' => $capacity,
            'power_watt' => $this->faker->optional()->numberBetween(700, 1200),
            'has_grill' => $this->faker->boolean(),
            'inverter' => $this->faker->boolean(50),
            'child_lock' => $this->faker->boolean(60),
            'energy_rating' => $this->faker->optional()->numberBetween(1, 5),
            'warranty_months' => $this->faker->numberBetween(12, 36),
            'thumbnail' => $this->faker->imageUrl(640, 480, 'technics', true, 'microwave'),
            'description' => $this->faker->optional()->paragraphs(3, true),
            'is_active' => true,
        ];
    }
}

