<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        $finalPrice = $this->sale_price ?? $this->price;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'price' => (float) $this->price,
            'sale_price' => $this->sale_price !== null ? (float) $this->sale_price : null,
            'final_price' => (float) $finalPrice,
            'thumbnail' => $this->thumbnail,
            'capacity_liters' => $this->capacity_liters,
            'power_watt' => $this->power_watt,
            'has_grill' => (bool) $this->has_grill,
            'inverter' => (bool) $this->inverter,
            'child_lock' => (bool) $this->child_lock,
            'energy_rating' => $this->energy_rating,
            'warranty_months' => $this->warranty_months,
            'is_active' => (bool) $this->is_active,
            'brand' => $this->whenLoaded('brand', function () {
                return [
                    'id' => $this->brand->id,
                    'name' => $this->brand->name,
                    'slug' => $this->brand->slug,
                ];
            }),
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                    'slug' => $this->category->slug,
                ];
            }),
        ];
    }
}

