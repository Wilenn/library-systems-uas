<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Aman dijalankan berulang kali: buku dicari berdasarkan judul, dan
     * buku yang sudah ada tidak diubah (stok hasil edit tidak tertimpa).
     */
    public function run(): void
    {
        $books = [
            ['title' => 'Pemrograman PHP', 'author' => 'Andi', 'year' => 2024, 'stock' => 5, 'category' => 'Pendidikan'],
            ['title' => 'Bumi', 'author' => 'Tere Liye', 'year' => 2014, 'stock' => 4, 'category' => 'Fantasi & Petualangan'],
            ['title' => 'Pulang', 'author' => 'Tere Liye', 'year' => 2015, 'stock' => 6, 'category' => 'Fiksi'],
            ['title' => 'Perahu Kertas', 'author' => 'Dee Lestari', 'year' => 2009, 'stock' => 3, 'category' => 'Romansa'],
            ['title' => 'Dilan: Dia adalah Dilanku Tahun 1990', 'author' => 'Pidi Baiq', 'year' => 2014, 'stock' => 7, 'category' => 'Romansa'],
        ];

        foreach ($books as $data) {
            $category = Category::firstOrCreate(['name' => $data['category']]);

            Book::firstOrCreate(
                ['title' => $data['title']],
                [
                    'author' => $data['author'],
                    'year' => $data['year'],
                    'stock' => $data['stock'],
                    'category_id' => $category->id,
                ]
            );
        }
    }
}
