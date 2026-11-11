<?php

namespace App\Http\Controllers;

use App\Http\Requests\BorrowingRequest;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BorrowingController extends Controller
{
    public function index(Request $request)
    {
        $term = trim($request->string('q')->toString());
        $status = $request->string('status')->toString();

        $borrowings = Borrowing::query()
            ->with(['member:id,name', 'user:id,name'])
            ->withCount('books')
            ->when($term !== '', fn ($query) => $query->whereHas(
                'member',
                fn ($member) => $member->where('name', 'like', "%{$term}%")
            ))
            ->when($status === 'borrowed', fn ($query) => $query->active())
            ->when($status === 'overdue', fn ($query) => $query->overdue())
            ->when($status === 'returned', fn ($query) => $query->returned())
            ->latest('borrowed_at')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('borrowings.index', compact('borrowings'));
    }

    public function create()
    {
        $members = Member::orderBy('name')->get(['id', 'name']);

        $books = Book::available()
            ->with('category:id,name')
            ->orderBy('title')
            ->get(['id', 'title', 'author', 'stock', 'category_id']);

        $dueDate = today()->addDays(Borrowing::LOAN_DAYS);

        return view('borrowings.create', compact('members', 'books', 'dueDate'));
    }

    /**
     * Mencatat peminjaman: semua perubahan (transaksi, daftar buku, pengurangan stok)
     * berhasil bersama atau gagal bersama. Baris buku dikunci selama transaksi supaya
     * dua petugas tidak bisa meminjam stok terakhir secara bersamaan.
     */
    public function store(BorrowingRequest $request)
    {
        $bookIds = $request->validated('book_ids');

        $borrowing = DB::transaction(function () use ($request, $bookIds) {
            $books = Book::whereIn('id', $bookIds)->lockForUpdate()->get();

            if ($books->count() !== count($bookIds)) {
                throw ValidationException::withMessages([
                    'book_ids' => 'Salah satu buku yang dipilih tidak tersedia.',
                ]);
            }

            $outOfStock = $books->filter(fn (Book $book) => $book->stock < 1);

            if ($outOfStock->isNotEmpty()) {
                throw ValidationException::withMessages([
                    'book_ids' => 'Stok habis untuk: '.$outOfStock->pluck('title')->join(', ').'.',
                ]);
            }

            $borrowing = Borrowing::create([
                'user_id' => $request->user()->id,
                'member_id' => $request->validated('member_id'),
                'borrowed_at' => today(),
                'due_date' => today()->addDays(Borrowing::LOAN_DAYS),
                'status' => Borrowing::STATUS_BORROWED,
            ]);

            $borrowing->books()->attach($books->pluck('id'));
            Book::whereIn('id', $books->pluck('id'))->decrement('stock');

            return $borrowing;
        });

        return redirect()->route('borrowings.show', $borrowing)
            ->with('success', 'Peminjaman berhasil dicatat.');
    }

    public function show(Borrowing $borrowing)
    {
        $borrowing->load(['member', 'user', 'books.category']);

        return view('borrowings.show', compact('borrowing'));
    }
}
