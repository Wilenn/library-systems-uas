<?php

namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    /**
     * Aman dijalankan berulang kali (dicari berdasarkan email).
     */
    public function run(): void
    {
        $members = [
            ['name' => 'Wisnu', 'email' => 'wisnu@example.com', 'phone' => '081200000001'],
            ['name' => 'Bilal', 'email' => 'bilal@example.com', 'phone' => '081200000002'],
            ['name' => 'Yuda', 'email' => 'yuda@example.com', 'phone' => '081200000003'],
            ['name' => 'Dimas', 'email' => 'dimas@example.com', 'phone' => '081200000004'],
            ['name' => 'Zorif', 'email' => 'zorif@example.com', 'phone' => '081200000005'],
        ];

        foreach ($members as $data) {
            Member::firstOrCreate(['email' => $data['email']], $data);
        }
    }
}
