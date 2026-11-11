@extends('layouts.app')

@section('title', 'Detail peminjaman')
@section('modern', '1')

@section('content')
    <x-page-header title="Detail peminjaman" :subtitle="'Transaksi #'.$borrowing->id">
        <x-button :href="route('borrowings.index')" variant="ghost">Kembali</x-button>
    </x-page-header>

    <x-card class="max-w-2xl">
        <dl class="grid grid-cols-3 gap-y-4 text-sm">
            <dt class="text-stone-500">Anggota</dt>
            <dd class="col-span-2">
                <a href="{{ route('members.show', $borrowing->member) }}" class="font-medium text-stone-900 hover:text-brand-700">{{ $borrowing->member->name }}</a>
            </dd>

            <dt class="text-stone-500">Dicatat oleh</dt>
            <dd class="col-span-2 text-stone-900">{{ $borrowing->user->name }}</dd>

            <dt class="text-stone-500">Tanggal pinjam</dt>
            <dd class="col-span-2 text-stone-900">{{ $borrowing->borrowed_at->format('d/m/Y') }}</dd>

            <dt class="text-stone-500">Jatuh tempo</dt>
            <dd @class(['col-span-2', 'font-medium text-red-600' => $borrowing->isOverdue(), 'text-stone-900' => ! $borrowing->isOverdue()])>
                {{ $borrowing->due_date->format('d/m/Y') }}
            </dd>

            <dt class="text-stone-500">Tanggal kembali</dt>
            <dd class="col-span-2 text-stone-900">{{ $borrowing->returned_at?->format('d/m/Y') ?? '-' }}</dd>

            <dt class="text-stone-500">Status</dt>
            <dd class="col-span-2"><x-borrowing-status :borrowing="$borrowing" /></dd>
        </dl>
    </x-card>

    <h2 class="mb-3 mt-8 text-base font-semibold text-stone-900">Buku yang dipinjam</h2>

    <x-table>
        <thead>
            <tr>
                <th>Judul</th>
                <th class="hidden sm:table-cell">Penulis</th>
                <th>Kategori</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($borrowing->books as $book)
                <tr>
                    <td>
                        <a href="{{ route('books.show', $book) }}" class="font-medium text-stone-900 hover:text-brand-700">{{ $book->title }}</a>
                    </td>
                    <td class="hidden sm:table-cell">{{ $book->author }}</td>
                    <td>{{ $book->category->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </x-table>
@endsection
