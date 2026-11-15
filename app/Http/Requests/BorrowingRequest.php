<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BorrowingRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Admin dan petugas boleh mencatat peminjaman; akses sudah dibatasi middleware 'auth'.
        return true;
    }

    /**
     * Pengecekan stok dilakukan di BorrowingController@store (di dalam transaksi
     * dengan penguncian baris), bukan di sini, agar tidak ada celah balapan data.
     */
    public function rules(): array
    {
        return [
            'member_id' => ['required', 'exists:members,id'],
            'book_ids' => ['required', 'array', 'min:1'],
            'book_ids.*' => ['integer', 'distinct', 'exists:books,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'member_id.required' => 'Pilih anggota yang meminjam.',
            'book_ids.required' => 'Pilih minimal satu buku.',
            'book_ids.min' => 'Pilih minimal satu buku.',
            'book_ids.*.distinct' => 'Satu buku tidak boleh dipilih lebih dari sekali.',
            'book_ids.*.exists' => 'Salah satu buku yang dipilih tidak tersedia.',
        ];
    }
}
