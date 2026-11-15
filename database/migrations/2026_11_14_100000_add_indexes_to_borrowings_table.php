<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Index (status, due_date) melayani filter "sedang dipinjam"/"terlambat" dan dashboard;
     * awalannya (status) menggantikan index status tunggal. Index borrowed_at melayani
     * pengurutan daftar peminjaman dari yang terbaru.
     */
    public function up(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->index(['status', 'due_date']);
            $table->index('borrowed_at');
        });
    }

    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->dropIndex(['status', 'due_date']);
            $table->dropIndex(['borrowed_at']);
            $table->index('status');
        });
    }
};
