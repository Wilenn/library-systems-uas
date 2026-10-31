<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Lapisan kedua di samping middleware 'role:admin' pada route.
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Dipakai untuk store dan update. Saat update, email milik user yang
     * sedang diedit dikecualikan dari unique, dan password boleh kosong
     * (artinya tidak diubah).
     */
    public function rules(): array
    {
        $user = $this->route('user'); // null saat membuat user baru

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'role' => ['required', Rule::in(User::ROLES)],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
