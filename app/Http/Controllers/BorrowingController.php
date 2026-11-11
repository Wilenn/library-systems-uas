<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use Illuminate\Http\Request;

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

    public function show(Borrowing $borrowing)
    {
        $borrowing->load(['member', 'user', 'books.category']);

        return view('borrowings.show', compact('borrowing'));
    }
}
