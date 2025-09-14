<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BrandStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return ($this->user()->role ?? 'CUSTOMER') === 'ADMIN';
    }

    public function rules(): array
    {
        return [
            'name' => ['required','string','max:255'],
            'slug' => ['nullable','string','max:255','unique:brands,slug'],
            'is_active' => ['boolean'],
        ];
    }
}

