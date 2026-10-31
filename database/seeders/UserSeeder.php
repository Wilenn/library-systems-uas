<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Akun contoh untuk pengembangan lokal. Password di-hash otomatis
     * oleh cast 'hashed' pada model User. Aman dijalankan berulang kali.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin Perpustakaan', 'password' => 'password', 'role' => User::ROLE_ADMIN]
        );

        User::updateOrCreate(
            ['email' => 'petugas@example.com'],
            ['name' => 'Petugas Perpustakaan', 'password' => 'password', 'role' => User::ROLE_PETUGAS]
        );
    }
}
