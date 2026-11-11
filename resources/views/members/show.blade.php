@extends('layouts.app')

@section('title', $member->name)
@section('modern', '1')

@section('content')
    <x-page-header :title="$member->name" subtitle="Detail anggota dan riwayat peminjaman.">
        <x-button :href="route('borrowings.create', ['member' => $member->id])">Catat peminjaman</x-button>
        <x-button :href="route('members.edit', $member)" variant="secondary">Edit</x-button>
        <x-button :href="route('members.index')" variant="ghost">Kembali</x-button>
    </x-page-header>

    <x-card class="max-w-xl">
        <dl class="grid grid-cols-3 gap-y-4 text-sm">
            <dt class="text-stone-500">Email</dt>
            <dd class="col-span-2 text-stone-900">{{ $member->email ?? '-' }}</dd>

            <dt class="text-stone-500">Telepon</dt>
            <dd class="col-span-2 text-stone-900">{{ $member->phone ?? '-' }}</dd>

            <dt class="text-stone-500">Alamat</dt>
            <dd class="col-span-2 whitespace-pre-line text-stone-900">{{ $member->address ?? '-' }}</dd>

            <dt class="text-stone-500">Terdaftar</dt>
            <dd class="col-span-2 text-stone-900">{{ $member->created_at->format('d/m/Y') }}</dd>
        </dl>
    </x-card>

    <h2 class="mb-3 mt-8 text-base font-semibold text-stone-900">Riwayat peminjaman</h2>

    @if ($borrowings->isEmpty())
        <x-empty-state title="Belum ada peminjaman" message="Anggota ini belum pernah meminjam buku." />
    @else
        <x-table>
            <thead>
                <tr>
                    <th>Tanggal pinjam</th>
                    <th>Jatuh tempo</th>
                    <th>Tanggal kembali</th>
                    <th>Buku</th>
                    <th>Status</th>
                    <th class="hidden md:table-cell">Petugas</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($borrowings as $borrowing)
                    <tr>
                        <td>{{ $borrowing->borrowed_at->format('d/m/Y') }}</td>
                        <td>{{ $borrowing->due_date->format('d/m/Y') }}</td>
                        <td>{{ $borrowing->returned_at?->format('d/m/Y') ?? '-' }}</td>
                        <td>{{ $borrowing->books->pluck('title')->join(', ') }}</td>
                        <td><x-borrowing-status :borrowing="$borrowing" /></td>
                        <td class="hidden md:table-cell">{{ $borrowing->user->name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>

        {{ $borrowings->links('pagination.simple') }}
    @endif
@endsection
