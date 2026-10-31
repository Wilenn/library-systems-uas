<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Member;

class DashboardController extends Controller
{
    public function index()
    {
        $jumlahBuku = Book::count();
        $jumlahMember = Member::count();
        $jumlahKategori = Category::count();

        return view('dashboard.index', compact(
            'jumlahBuku',
            'jumlahMember',
            'jumlahKategori'
        ));
    }
}
