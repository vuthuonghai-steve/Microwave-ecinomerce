<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CategoryStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return ($this->user()->role ?? 'CUSTOMER') === 'ADMIN';
    }

    public function rules(): array
    {
        return [
            'parent_id' => ['nullable','integer','exists:categories,id'],
            'name' => ['required','string','max:255'],
            'slug' => ['nullable','string','max:255','unique:categories,slug'],
            'is_active' => ['boolean'],
        ];
    }
}

