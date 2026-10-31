<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Judul buku sudah dicek unik di validasi; index ini menjaga
     * aturan yang sama di level database (mencegah duplikat dari
     * seeder, tinker, atau request yang bersamaan).
     *
     * CATATAN: migration gagal jika tabel books sudah berisi judul ganda.
     * Cek dulu dengan:
     *   SELECT title, COUNT(*) FROM books GROUP BY title HAVING COUNT(*) > 1;
     */
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->unique('title');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropUnique(['title']);
        });
    }
};
