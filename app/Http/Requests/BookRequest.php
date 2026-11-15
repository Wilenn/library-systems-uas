<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Admin dan petugas boleh mengelola buku; akses sudah dibatasi middleware 'auth'.
        return true;
    }

    /**
     * Dipakai untuk store dan update. Saat update, judul milik buku
     * yang sedang diedit dikecualikan dari pengecekan unique.
     * Pesan error umum ada di lang/id/validation.php.
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required', 'string', 'min:3', 'max:255',
                Rule::unique('books', 'title')->ignore($this->route('book')),
            ],
            'author' => ['required', 'string', 'min:3', 'max:255', "regex:/^[\pL\s\.\,\'\-]+$/u"],
            // Kolom MySQL YEAR hanya menerima 1901-2155, jadi batas bawah 1901.
            'year' => ['required', 'integer', 'digits:4', 'between:1901,'.date('Y')],
            'stock' => ['required', 'integer', 'min:0', 'max:1000'],
            'category_id' => ['required', 'exists:categories,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'author.regex' => 'Nama penulis hanya boleh berisi huruf, spasi, titik, koma, apostrof, dan tanda hubung.',
        ];
    }
}
