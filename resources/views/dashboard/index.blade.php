@extends('layouts.app')

@section('title', 'Dashboard')
@section('modern', '1')

@section('content')
    <x-page-header title="Dashboard" :subtitle="'Halo, '.auth()->user()->name.'. Ringkasan perpustakaan hari ini.'">
        <x-button :href="route('borrowings.create')">Catat peminjaman</x-button>
    </x-page-header>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat label="Judul buku" :value="$jumlahJudul" :href="route('books.index')" />
        <x-stat label="Total stok buku" :value="$jumlahStok" :href="route('books.index')" hint="Eksemplar yang tersedia" />
        <x-stat label="Anggota" :value="$jumlahMember" :href="route('members.index')" />
        <x-stat label="Sedang dipinjam" :value="$jumlahDipinjam" :href="route('borrowings.index', ['status' => 'borrowed'])" hint="Transaksi belum dikembalikan" />
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        {{-- Peminjaman yang melewati jatuh tempo --}}
        <section>
            <div class="mb-3 flex items-baseline justify-between gap-3">
                <h2 class="text-base font-semibold text-stone-900">
                    Terlambat dikembalikan
                    @if ($jumlahTerlambat > 0)
                        <x-badge color="red" class="ml-1">{{ $jumlahTerlambat }}</x-badge>
                    @endif
                </h2>

                @if ($jumlahTerlambat > 0)
                    <a href="{{ route('borrowings.index', ['status' => 'overdue']) }}" class="text-sm text-brand-700 hover:underline">Lihat semua</a>
                @endif
            </div>

            <div class="rounded-lg border border-stone-200 bg-white shadow-sm">
                @if ($terlambat->isEmpty())
                    <p class="px-5 py-8 text-center text-sm text-stone-500">Tidak ada peminjaman yang terlambat.</p>
                @else
                    <ul class="divide-y divide-stone-100">
                        @foreach ($terlambat as $borrowing)
                            <li>
                                <a href="{{ route('borrowings.show', $borrowing) }}" class="flex items-center justify-between gap-3 px-5 py-3 hover:bg-stone-50">
                                    <span class="min-w-0">
                                        <span class="block truncate text-sm font-medium text-stone-900">{{ $borrowing->member->name }}</span>
                                        <span class="block text-xs text-stone-500">{{ $borrowing->books_count }} buku · jatuh tempo {{ $borrowing->due_date->format('d/m/Y') }}</span>
                                    </span>
                                    <x-badge color="red">{{ $borrowing->daysOverdue() }} hari</x-badge>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </section>

        {{-- Peminjaman terbaru --}}
        <section>
            <div class="mb-3 flex items-baseline justify-between gap-3">
                <h2 class="text-base font-semibold text-stone-900">Peminjaman terbaru</h2>

                @if ($terbaru->isNotEmpty())
                    <a href="{{ route('borrowings.index') }}" class="text-sm text-brand-700 hover:underline">Lihat semua</a>
                @endif
            </div>

            <div class="rounded-lg border border-stone-200 bg-white shadow-sm">
                @if ($terbaru->isEmpty())
                    <p class="px-5 py-8 text-center text-sm text-stone-500">Belum ada peminjaman.</p>
                @else
                    <ul class="divide-y divide-stone-100">
                        @foreach ($terbaru as $borrowing)
                            <li>
                                <a href="{{ route('borrowings.show', $borrowing) }}" class="flex items-center justify-between gap-3 px-5 py-3 hover:bg-stone-50">
                                    <span class="min-w-0">
                                        <span class="block truncate text-sm font-medium text-stone-900">{{ $borrowing->member->name }}</span>
                                        <span class="block text-xs text-stone-500">{{ $borrowing->borrowed_at->format('d/m/Y') }} · {{ $borrowing->books_count }} buku</span>
                                    </span>
                                    <x-borrowing-status :borrowing="$borrowing" />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </section>
    </div>
@endsection
