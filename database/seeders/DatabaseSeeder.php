<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // firstOrCreate agar `db:seed` bisa dijalankan berulang tanpa error.
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => 'password'] // di-hash otomatis oleh cast 'hashed'
        );

        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            MemberSeeder::class,
            BookSeeder::class,
        ]);
    }
}
