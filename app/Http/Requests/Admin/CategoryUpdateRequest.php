<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return ($this->user()->role ?? 'CUSTOMER') === 'ADMIN';
    }

    public function rules(): array
    {
        $id = (int) $this->route('id');
        return [
            'parent_id' => ['nullable','integer','exists:categories,id','not_in:'.$id],
            'name' => ['sometimes','string','max:255'],
            'slug' => ['sometimes','string','max:255', Rule::unique('categories','slug')->ignore($id)],
            'is_active' => ['boolean'],
        ];
    }
}

