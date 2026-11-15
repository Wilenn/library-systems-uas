<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;

class DashboardController extends Controller
{
    /** Banyak baris pada daftar "terlambat" dan "terbaru" di dashboard. */
    private const LIST_LIMIT = 5;

    public function index()
    {
        // Judul dan total stok diambil dalam satu query agregat.
        $books = Book::toBase()
            ->selectRaw('count(*) as titles, coalesce(sum(stock), 0) as stock')
            ->first();

        return view('dashboard.index', [
            'jumlahJudul' => (int) $books->titles,
            'jumlahStok' => (int) $books->stock,
            'jumlahMember' => Member::count(),
            // Transaksi yang bukunya belum dikembalikan (termasuk yang terlambat).
            'jumlahDipinjam' => Borrowing::active()->count(),

            'jumlahTerlambat' => Borrowing::overdue()->count(),
            // Yang paling lama terlambat ditampilkan paling atas.
            'terlambat' => Borrowing::overdue()
                ->with('member:id,name')
                ->withCount('books')
                ->orderBy('due_date')
                ->orderBy('id')
                ->limit(self::LIST_LIMIT)
                ->get(),
            'terbaru' => Borrowing::query()
                ->with('member:id,name')
                ->withCount('books')
                ->latest('borrowed_at')
                ->latest('id')
                ->limit(self::LIST_LIMIT)
                ->get(),
        ]);
    }
}
