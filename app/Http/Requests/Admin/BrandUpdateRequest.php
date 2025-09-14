<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BrandUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return ($this->user()->role ?? 'CUSTOMER') === 'ADMIN';
    }

    public function rules(): array
    {
        $id = (int) $this->route('id');
        return [
            'name' => ['sometimes','string','max:255'],
            'slug' => ['sometimes','string','max:255', Rule::unique('brands','slug')->ignore($id)],
            'is_active' => ['boolean'],
        ];
    }
}

