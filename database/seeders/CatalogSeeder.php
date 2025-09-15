<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        // Brands
        $brandNames = ['Panasonic', 'Sharp', 'Electrolux', 'LG', 'Samsung', 'Midea', 'Toshiba'];
        $brands = collect($brandNames)->map(function ($name) {
            return Brand::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'is_active' => true]
            );
        });

        // Categories (flat + a few nested)
        $root = Category::firstOrCreate(
            ['slug' => 'lo-vi-song'],
            ['name' => 'Lò vi sóng', 'is_active' => true]
        );

        $catNames = [
            'co' => 'Lò vi sóng cơ',
            'dien-tu' => 'Lò vi sóng điện tử',
            'inverter' => 'Lò vi sóng inverter',
            'nuong' => 'Lò vi sóng có nướng',
        ];
        $categories = collect($catNames)->map(function ($label, $slug) use ($root) {
            return Category::firstOrCreate(
                ['slug' => $slug],
                ['name' => $label, 'parent_id' => $root->id, 'is_active' => true]
            );
        });

        // Products + Stocks
        $thumbnails = [
            'https://img.youtube.com/vi/WDLoqfOLHRQ/mqdefault.jpg',
            'https://img.youtube.com/vi/Lbg-ULaCDKI/mqdefault.jpg',
        ];
        $total = 30;
        Product::factory($total)
            ->state(function () use ($brands, $categories, $thumbnails) {
                return [
                    'brand_id' => $brands->random()->id,
                    'category_id' => $categories->random()->id,
                    'thumbnail' => $thumbnails[array_rand($thumbnails)],
                ];
            })
            ->create()
            ->each(function (Product $p) {
                ProductStock::factory()->create(['product_id' => $p->id]);
            });
    }
}
