<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Akses sudah dibatasi middleware 'auth' di routes/web.php.
        return true;
    }

    /**
     * Dipakai untuk store dan update. Saat update, judul milik buku
     * yang sedang diedit dikecualikan dari pengecekan unique.
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

    // Pesan error dalam Bahasa Indonesia
    public function messages(): array
    {
        return [
            'title.required' => 'Judul buku wajib diisi.',
            'title.min' => 'Judul buku minimal 3 karakter.',
            'title.unique' => 'Judul buku ini sudah terdaftar.',
            'author.required' => 'Nama penulis wajib diisi.',
            'author.min' => 'Nama penulis minimal 3 karakter.',
            'author.regex' => 'Nama penulis hanya boleh berisi huruf, spasi, titik, koma, apostrof, dan tanda hubung.',
            'year.required' => 'Tahun terbit wajib diisi.',
            'year.integer' => 'Tahun terbit harus berupa angka.',
            'year.digits' => 'Tahun terbit harus 4 digit.',
            'year.between' => 'Tahun terbit harus antara 1901 sampai '.date('Y').'.',
            'stock.required' => 'Stok wajib diisi.',
            'stock.integer' => 'Stok harus berupa bilangan bulat.',
            'stock.min' => 'Stok tidak boleh negatif.',
            'stock.max' => 'Stok maksimal 1000.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak tersedia.',
        ];
    }
}
