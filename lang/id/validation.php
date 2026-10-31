<?php

/*
| Pesan validasi Bahasa Indonesia. Hanya berisi aturan yang dipakai
| aplikasi ini; tambahkan di sini jika memakai aturan validasi baru.
*/

return [
    'required' => ':Attribute wajib diisi.',
    'string' => ':Attribute harus berupa teks.',
    'email' => ':Attribute harus berupa alamat email yang valid.',
    'unique' => ':Attribute sudah terdaftar.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'in' => ':Attribute yang dipilih tidak valid.',
    'exists' => ':Attribute yang dipilih tidak tersedia.',
    'integer' => ':Attribute harus berupa bilangan bulat.',
    'digits' => ':Attribute harus terdiri dari :digits digit.',
    'regex' => 'Format :attribute tidak valid.',
    'date' => ':Attribute bukan tanggal yang valid.',
    'array' => ':Attribute harus berupa daftar.',
    'distinct' => ':Attribute memiliki nilai ganda.',

    'min' => [
        'numeric' => ':Attribute minimal :min.',
        'string' => ':Attribute minimal :min karakter.',
        'array' => ':Attribute minimal berisi :min item.',
    ],
    'max' => [
        'numeric' => ':Attribute maksimal :max.',
        'string' => ':Attribute maksimal :max karakter.',
        'array' => ':Attribute maksimal berisi :max item.',
    ],
    'between' => [
        'numeric' => ':Attribute harus antara :min dan :max.',
        'string' => ':Attribute harus antara :min dan :max karakter.',
    ],

    'attributes' => [
        'name' => 'nama',
        'email' => 'email',
        'password' => 'password',
        'role' => 'role',
        'title' => 'judul',
        'author' => 'penulis',
        'year' => 'tahun terbit',
        'stock' => 'stok',
        'category_id' => 'kategori',
        'phone' => 'nomor telepon',
        'address' => 'alamat',
        'member_id' => 'anggota',
        'book_ids' => 'buku',
    ],
];
