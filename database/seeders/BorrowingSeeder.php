<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Seeder;

class BorrowingSeeder extends Seeder
{
    /**
     * Contoh transaksi agar dashboard dan daftar peminjaman langsung berisi:
     * 1 aktif, 2 terlambat, dan 2 sudah dikembalikan. Tanggal dihitung dari hari ini
     * sehingga contoh selalu relevan, dan stok buku dikurangi hanya untuk transaksi aktif.
     *
     * Aman dijalankan berulang kali: dilewati jika sudah ada data peminjaman,
     * jadi tidak akan menimpa atau menggandakan transaksi nyata.
     */
    public function run(): void
    {
        if (Borrowing::exists()) {
            return;
        }

        $officer = User::where('email', 'petugas@example.com')->firstOrFail();

        // [email anggota, judul buku, dipinjam berapa hari lalu, dikembalikan berapa hari lalu]
        $transactions = [
            ['wisnu@example.com', ['Laskar Pelangi', 'Pulang'], 2, null],           // aktif, belum jatuh tempo
            ['bilal@example.com', ['Bumi Manusia'], 12, null],                      // terlambat 5 hari
            ['yuda@example.com', ['Atomic Habits', 'Filosofi Teras'], 10, null],    // terlambat 3 hari
            ['dimas@example.com', ['Sapiens'], 20, 15],                             // sudah dikembalikan
            ['zorif@example.com', ['Negeri 5 Menara', 'One Piece Vol. 1'], 9, 4],   // sudah dikembalikan
        ];

        foreach ($transactions as [$email, $titles, $borrowedDaysAgo, $returnedDaysAgo]) {
            $this->record($officer, Member::where('email', $email)->firstOrFail(), $titles, $borrowedDaysAgo, $returnedDaysAgo);
        }
    }

    private function record(User $officer, Member $member, array $titles, int $borrowedDaysAgo, ?int $returnedDaysAgo): void
    {
        $books = Book::whereIn('title', $titles)->get();
        $borrowedAt = today()->subDays($borrowedDaysAgo);
        $returned = $returnedDaysAgo !== null;

        $borrowing = Borrowing::create([
            'user_id' => $officer->id,
            'member_id' => $member->id,
            'borrowed_at' => $borrowedAt,
            'due_date' => $borrowedAt->copy()->addDays(Borrowing::LOAN_DAYS),
            'returned_at' => $returned ? today()->subDays($returnedDaysAgo) : null,
            'status' => $returned ? Borrowing::STATUS_RETURNED : Borrowing::STATUS_BORROWED,
        ]);

        $borrowing->books()->attach($books->pluck('id'));

        // Buku yang masih dipinjam tidak ada di rak, jadi stoknya berkurang.
        if (! $returned) {
            Book::whereIn('id', $books->pluck('id'))->decrement('stock');
        }
    }
}
