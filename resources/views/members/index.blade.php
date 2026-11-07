@extends('layouts.app')

@section('title', 'Anggota')
@section('modern', '1')

@section('content')
    @php($filtered = filled(request('q')))

    <x-page-header title="Anggota" subtitle="Daftar anggota yang dapat meminjam buku.">
        <x-button :href="route('members.create')">Tambah anggota</x-button>
    </x-page-header>

    <form action="{{ route('members.index') }}" method="GET" class="mb-5 flex flex-wrap items-end gap-3">
        <div class="w-full sm:w-72">
            <x-field name="q" label="Cari" :value="request('q')" placeholder="Nama, email, atau telepon" />
        </div>

        <div class="flex gap-2">
            <x-button type="submit">Cari</x-button>
            @if ($filtered)
                <x-button :href="route('members.index')" variant="secondary">Reset</x-button>
            @endif
        </div>
    </form>

    @if ($members->isEmpty())
        @if ($filtered)
            <x-empty-state title="Anggota tidak ditemukan" message="Coba kata kunci lain.">
                <x-button :href="route('members.index')" variant="secondary">Reset pencarian</x-button>
            </x-empty-state>
        @else
            <x-empty-state title="Belum ada anggota" message="Tambahkan anggota pertama agar bisa meminjam buku.">
                <x-button :href="route('members.create')">Tambah anggota</x-button>
            </x-empty-state>
        @endif
    @else
        <x-table>
            <thead>
                <tr>
                    <th>Anggota</th>
                    <th class="hidden sm:table-cell">Telepon</th>
                    <th>Sedang meminjam</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($members as $member)
                    <tr>
                        <td>
                            <a href="{{ route('members.show', $member) }}" class="font-medium text-stone-900 hover:text-brand-700">{{ $member->name }}</a>
                            <p class="text-xs text-stone-500">{{ $member->email ?? '-' }}</p>
                        </td>
                        <td class="hidden sm:table-cell">{{ $member->phone ?? '-' }}</td>
                        <td>
                            @if ($member->active_borrowings_count > 0)
                                <x-badge color="amber">{{ $member->active_borrowings_count }} transaksi</x-badge>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <x-button :href="route('members.edit', $member)" variant="secondary" size="sm">Edit</x-button>

                                <form action="{{ route('members.destroy', $member) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <x-button type="submit" variant="danger" size="sm"
                                        onclick="return confirm('Hapus anggota ini?')">Hapus</x-button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>

        {{ $members->links('pagination.simple') }}
    @endif
@endsection
