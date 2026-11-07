<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Admin dan petugas boleh mengelola anggota; akses sudah dibatasi middleware 'auth'.
        return true;
    }

    /**
     * Dipakai untuk store dan update. Saat update, email milik anggota
     * yang sedang diedit dikecualikan dari pengecekan unique.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('members', 'email')->ignore($this->route('member'))],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
            'address' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Nomor telepon hanya boleh berisi angka, spasi, tanda +, -, dan tanda kurung.',
        ];
    }
}
