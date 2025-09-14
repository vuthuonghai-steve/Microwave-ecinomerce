<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return ($this->user()->role ?? 'CUSTOMER') === 'ADMIN';
    }

    public function rules(): array
    {
        $id = (int) $this->route('id');
        return [
            'category_id' => ['sometimes','integer','exists:categories,id'],
            'brand_id' => ['sometimes','integer','exists:brands,id'],
            'name' => ['sometimes','string','max:255'],
            'slug' => ['sometimes','string','max:255', Rule::unique('products','slug')->ignore($id)],
            'price' => ['sometimes','numeric','min:0'],
            'sale_price' => ['nullable','numeric','min:0'],
            'capacity_liters' => ['sometimes','integer','min:0'],
            'power_watt' => ['nullable','integer','min:0'],
            'has_grill' => ['boolean'],
            'inverter' => ['boolean'],
            'child_lock' => ['boolean'],
            'energy_rating' => ['nullable','integer','min:1','max:5'],
            'warranty_months' => ['sometimes','integer','min:0'],
            'thumbnail' => ['nullable','string'],
            'description' => ['nullable','string'],
            'is_active' => ['boolean'],
        ];
    }
}

