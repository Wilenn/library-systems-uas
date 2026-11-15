<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->paginate(10);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(UserRequest $request)
    {
        User::create($request->validated());

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();

        // Password kosong berarti tidak diubah.
        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        // Admin tidak boleh mengubah role akunnya sendiri, supaya tidak
        // mengunci dirinya (dan sistem) dari fitur admin.
        if ($user->is($request->user())) {
            $data['role'] = $user->role;
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'Anda tidak dapat menghapus akun yang sedang dipakai.');
        }

        if ($user->borrowings()->exists()) {
            return back()->with('error', 'User tidak dapat dihapus karena sudah mencatat transaksi peminjaman.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
