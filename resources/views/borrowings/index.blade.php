@extends('layouts.app')

@section('title', 'Peminjaman')
@section('modern', '1')

@section('content')
    @php($filtered = filled(request('q')) || filled(request('status')))

    <x-page-header title="Peminjaman" subtitle="Riwayat dan status transaksi peminjaman buku." />

    <form action="{{ route('borrowings.index') }}" method="GET" class="mb-5 flex flex-wrap items-end gap-3">
        <div class="w-full sm:w-64">
            <x-field name="q" label="Nama anggota" :value="request('q')" placeholder="Cari anggota" />
        </div>

        <div class="w-full sm:w-52">
            <x-field name="status" label="Status" type="select">
                <option value="">Semua status</option>
                <option value="borrowed" @selected(request('status') === 'borrowed')>Sedang dipinjam</option>
                <option value="overdue" @selected(request('status') === 'overdue')>Terlambat</option>
                <option value="returned" @selected(request('status') === 'returned')>Sudah dikembalikan</option>
            </x-field>
        </div>

        <div class="flex gap-2">
            <x-button type="submit">Cari</x-button>
            @if ($filtered)
                <x-button :href="route('borrowings.index')" variant="secondary">Reset</x-button>
            @endif
        </div>
    </form>

    @if ($borrowings->isEmpty())
        @if ($filtered)
            <x-empty-state title="Peminjaman tidak ditemukan" message="Coba ubah nama anggota atau status.">
                <x-button :href="route('borrowings.index')" variant="secondary">Reset pencarian</x-button>
            </x-empty-state>
        @else
            <x-empty-state title="Belum ada peminjaman" message="Transaksi peminjaman akan muncul di sini." />
        @endif
    @else
        <x-table>
            <thead>
                <tr>
                    <th>Anggota</th>
                    <th class="hidden sm:table-cell">Tanggal pinjam</th>
                    <th>Jatuh tempo</th>
                    <th class="hidden md:table-cell">Buku</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($borrowings as $borrowing)
                    <tr>
                        <td>
                            <a href="{{ route('members.show', $borrowing->member) }}" class="font-medium text-stone-900 hover:text-brand-700">{{ $borrowing->member->name }}</a>
                            <p class="text-xs text-stone-500">oleh {{ $borrowing->user->name }}</p>
                        </td>
                        <td class="hidden sm:table-cell">{{ $borrowing->borrowed_at->format('d/m/Y') }}</td>
                        <td @class(['font-medium text-red-600' => $borrowing->isOverdue()])>{{ $borrowing->due_date->format('d/m/Y') }}</td>
                        <td class="hidden md:table-cell">{{ $borrowing->books_count }} buku</td>
                        <td><x-borrowing-status :borrowing="$borrowing" /></td>
                        <td>
                            <x-button :href="route('borrowings.show', $borrowing)" variant="secondary" size="sm">Detail</x-button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>

        {{ $borrowings->links('pagination.simple') }}
    @endif
@endsection
