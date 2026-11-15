<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberRequest;
use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $members = Member::query()
            ->search($request->query('q'))
            ->withCount(['borrowings as active_borrowings_count' => fn ($query) => $query->active()])
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('members.index', compact('members'));
    }

    public function create()
    {
        return view('members.create');
    }

    public function store(MemberRequest $request)
    {
        Member::create($request->validated());

        return redirect()->route('members.index')
            ->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function show(Member $member)
    {
        $borrowings = $member->borrowings()
            ->with(['books:id,title', 'user:id,name'])
            ->latest('borrowed_at')
            ->latest('id')
            ->paginate(10);

        return view('members.show', compact('member', 'borrowings'));
    }

    public function edit(Member $member)
    {
        return view('members.edit', compact('member'));
    }

    public function update(MemberRequest $request, Member $member)
    {
        $member->update($request->validated());

        return redirect()->route('members.index')
            ->with('success', 'Anggota berhasil diperbarui.');
    }

    public function destroy(Member $member)
    {
        if ($member->borrowings()->exists()) {
            return back()->with('error', 'Anggota tidak dapat dihapus karena memiliki riwayat peminjaman.');
        }

        $member->delete();

        return redirect()->route('members.index')
            ->with('success', 'Anggota berhasil dihapus.');
    }
}
