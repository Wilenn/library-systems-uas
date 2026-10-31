<?php

namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    /**
     * Aman dijalankan berulang kali (firstOrCreate tidak membuat duplikat).
     */
    public function run(): void
    {
        foreach (['Wisnu', 'Bilal', 'Yuda', 'Dimas', 'Zorif'] as $name) {
            Member::firstOrCreate(['name' => $name]);
        }
    }
}
