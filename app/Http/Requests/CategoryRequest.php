<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Lapisan kedua di samping middleware 'role:admin' pada route.
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Dipakai untuk store dan update. Saat update, nama milik kategori
     * yang sedang diedit dikecualikan dari pengecekan unique.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'min:2', 'max:255',
                Rule::unique('categories', 'name')->ignore($this->route('category')),
            ],
        ];
    }
}
