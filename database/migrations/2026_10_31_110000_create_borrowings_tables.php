<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrowings', function (Blueprint $table) {
            $table->id();
            // Petugas/admin yang mencatat transaksi.
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('member_id')->constrained()->restrictOnDelete();
            $table->date('borrowed_at');
            $table->date('due_date');
            $table->date('returned_at')->nullable();
            $table->string('status', 20)->default('borrowed')->index(); // borrowed | returned
            $table->timestamps();
        });

        Schema::create('book_borrowing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrowing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained()->restrictOnDelete();
            // Satu buku hanya boleh muncul sekali dalam satu transaksi.
            $table->unique(['borrowing_id', 'book_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_borrowing');
        Schema::dropIfExists('borrowings');
    }
};
