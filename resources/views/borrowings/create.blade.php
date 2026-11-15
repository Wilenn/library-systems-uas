@extends('layouts.app')

@section('title', 'Catat peminjaman')

@section('content')
    @php($selected = array_map('intval', (array) old('book_ids', [])))

    <x-page-header title="Catat peminjaman" subtitle="Pilih anggota dan buku yang dipinjam." />

    @if ($members->isEmpty() || $books->isEmpty())
        <x-alert type="info" class="mb-4 max-w-2xl">
            @if ($members->isEmpty())
                Belum ada anggota. <a href="{{ route('members.create') }}" class="font-medium underline">Tambah anggota</a> terlebih dahulu.
            @endif
            @if ($books->isEmpty())
                Tidak ada buku dengan stok tersedia.
            @endif
        </x-alert>
    @endif

    <form action="{{ route('borrowings.store') }}" method="POST">
        @csrf

        <x-card class="max-w-2xl">
            <x-field name="member_id" label="Anggota" type="select" required>
                <option value="">Pilih anggota</option>
                @foreach ($members as $member)
                    <option value="{{ $member->id }}" @selected((int) old('member_id', request('member')) === $member->id)>{{ $member->name }}</option>
                @endforeach
            </x-field>

            <p class="mt-3 text-sm text-stone-500">
                Tanggal pinjam hari ini ({{ today()->format('d/m/Y') }}).
                Jatuh tempo otomatis {{ $dueDate->format('d/m/Y') }} ({{ \App\Models\Borrowing::LOAN_DAYS }} hari).
            </p>
        </x-card>

        <x-card class="mt-4 max-w-2xl">
            <p class="mb-2 text-sm font-medium text-stone-700">Buku yang dipinjam</p>

            <input type="search" id="book-filter" placeholder="Cari judul atau penulis" autocomplete="off"
                class="block w-full rounded-md border border-stone-300 bg-white px-3 py-2 text-sm shadow-sm placeholder:text-stone-400 focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">

            <ul class="mt-3 max-h-80 divide-y divide-stone-100 overflow-y-auto rounded-md border border-stone-200">
                @forelse ($books as $book)
                    <li data-book-item data-search="{{ mb_strtolower($book->title.' '.$book->author) }}">
                        <label class="flex cursor-pointer items-start gap-3 px-3 py-2.5 hover:bg-stone-50">
                            <input type="checkbox" name="book_ids[]" value="{{ $book->id }}" @checked(in_array($book->id, $selected, true))
                                class="mt-1 rounded border-stone-300 text-brand-700 focus:ring-brand-600">
                            <span class="flex-1 text-sm">
                                <span class="font-medium text-stone-900">{{ $book->title }}</span>
                                <span class="block text-xs text-stone-500">{{ $book->author }} · {{ $book->category->name }}</span>
                            </span>
                            <x-badge color="green">Stok {{ $book->stock }}</x-badge>
                        </label>
                    </li>
                @empty
                    <li class="px-3 py-6 text-center text-sm text-stone-500">Tidak ada buku yang tersedia.</li>
                @endforelse
            </ul>

            @if ($errors->has('book_ids') || $errors->has('book_ids.*'))
                <p class="mt-2 text-sm text-red-600">{{ $errors->first('book_ids') ?: $errors->first('book_ids.*') }}</p>
            @endif
        </x-card>

        <div class="mt-6 flex items-center gap-2">
            <x-button type="submit">Simpan peminjaman</x-button>
            <x-button :href="route('borrowings.index')" variant="secondary">Batal</x-button>
        </div>
    </form>
@endsection
