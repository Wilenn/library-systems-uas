<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Aman dijalankan berulang kali: buku dicari berdasarkan judul, dan
     * buku yang sudah ada tidak diubah.
     */
    public function run(): void
    {
        $books = [
            ['Laskar Pelangi', 'Andrea Hirata', 2005, 5, 'Fiksi'],
            ['Pulang', 'Tere Liye', 2015, 6, 'Fiksi'],
            ['Negeri 5 Menara', 'Ahmad Fuadi', 2009, 4, 'Fiksi'],
            ['Bumi Manusia', 'Pramoedya Ananta Toer', 1980, 3, 'Fiksi'],
            ['Sapiens', 'Yuval Noah Harari', 2011, 4, 'Non-Fiksi'],
            ['Pemrograman PHP', 'Andi', 2024, 5, 'Pendidikan'],
            ['One Piece Vol. 1', 'Eiichiro Oda', 1997, 7, 'Komik & Manga'],
            ['Perahu Kertas', 'Dee Lestari', 2009, 3, 'Romansa'],
            ['Dilan: Dia adalah Dilanku Tahun 1990', 'Pidi Baiq', 2014, 7, 'Romansa'],
            ['Bumi', 'Tere Liye', 2014, 4, 'Fantasi & Petualangan'],
            ['Filosofi Teras', 'Henry Manampiring', 2018, 6, 'Pengembangan Diri'],
            ['Atomic Habits', 'James Clear', 2018, 5, 'Pengembangan Diri'],
            ['Hujan Bulan Juni', 'Sapardi Djoko Damono', 1994, 2, 'Puisi & Musik'],
        ];

        foreach ($books as [$title, $author, $year, $stock, $categoryName]) {
            $category = Category::firstOrCreate(['name' => $categoryName]);

            Book::firstOrCreate(
                ['title' => $title],
                [
                    'author' => $author,
                    'year' => $year,
                    'stock' => $stock,
                    'category_id' => $category->id,
                ]
            );
        }
    }
}
