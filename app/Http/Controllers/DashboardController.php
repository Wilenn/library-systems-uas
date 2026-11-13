<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;

class DashboardController extends Controller
{
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
        ]);
    }
}
