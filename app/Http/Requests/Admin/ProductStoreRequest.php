<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return ($this->user()->role ?? 'CUSTOMER') === 'ADMIN';
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required','integer','exists:categories,id'],
            'brand_id' => ['required','integer','exists:brands,id'],
            'name' => ['required','string','max:255'],
            'slug' => ['nullable','string','max:255','unique:products,slug'],
            'price' => ['required','numeric','min:0'],
            'sale_price' => ['nullable','numeric','min:0'],
            'capacity_liters' => ['required','integer','min:0'],
            'power_watt' => ['nullable','integer','min:0'],
            'has_grill' => ['boolean'],
            'inverter' => ['boolean'],
            'child_lock' => ['boolean'],
            'energy_rating' => ['nullable','integer','min:1','max:5'],
            'warranty_months' => ['required','integer','min:0'],
            'thumbnail' => ['nullable','string'],
            'description' => ['nullable','string'],
            'is_active' => ['boolean'],
        ];
    }
}

