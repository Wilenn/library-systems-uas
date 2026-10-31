@extends('layouts.app')

@section('content')
    <h2>Daftar User</h2>

    <a href="{{ route('users.create') }}">+ Tambah User</a>

    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr>
                    <td>{{ $users->firstItem() + $loop->index }}</td>
                    <td>
                        {{ $user->name }}
                        @if ($user->is(auth()->user()))
                            (Anda)
                        @endif
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>{{ ucfirst($user->role) }}</td>
                    <td>
                        <a href="{{ route('users.edit', $user) }}">Edit</a>
                        @unless ($user->is(auth()->user()))
                            <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Hapus user ini?')">Hapus</button>
                            </form>
                        @endunless
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Belum ada data user.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $users->links('pagination.simple') }}
@endsection
