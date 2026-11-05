@extends('layouts.app')

@section('title', 'Pengguna')
@section('modern', '1')

@section('content')
    <x-page-header title="Pengguna" subtitle="Kelola akun admin dan petugas.">
        <x-button :href="route('users.create')">Tambah pengguna</x-button>
    </x-page-header>

    @if ($users->isEmpty())
        <x-empty-state title="Belum ada pengguna" message="Tambahkan pengguna pertama untuk mulai." />
    @else
        <x-table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td class="font-medium text-stone-900">
                            {{ $user->name }}
                            @if ($user->is(auth()->user()))
                                <x-badge class="ml-1">Anda</x-badge>
                            @endif
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <x-badge :color="$user->isAdmin() ? 'brand' : 'neutral'">{{ ucfirst($user->role) }}</x-badge>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <x-button :href="route('users.edit', $user)" variant="secondary" size="sm">Edit</x-button>

                                @unless ($user->is(auth()->user()))
                                    <form action="{{ route('users.destroy', $user) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <x-button type="submit" variant="danger" size="sm"
                                            onclick="return confirm('Hapus pengguna ini?')">Hapus</x-button>
                                    </form>
                                @endunless
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>

        {{ $users->links('pagination.simple') }}
    @endif
@endsection
