<?php

namespace Database\Factories;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Brand>
 */
class BrandFactory extends Factory
{
    protected $model = Brand::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement([
            'Panasonic', 'Sharp', 'Electrolux', 'LG', 'Samsung', 'Midea', 'Toshiba', 'Sanyo', 'Bosch'
        ]) . ' ' . $this->faker->randomNumber(3);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'is_active' => true,
        ];
    }
}

