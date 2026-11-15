@extends('layouts.app')

@section('title', 'Buku')

@section('content')
    @php($filtered = filled(request('q')) || filled(request('category')))

    <x-page-header title="Buku" subtitle="Kelola koleksi buku perpustakaan.">
        <x-button :href="route('books.create')">Tambah buku</x-button>
    </x-page-header>

    <form action="{{ route('books.index') }}" method="GET" class="mb-5 flex flex-wrap items-end gap-3">
        <div class="w-full sm:w-72">
            <x-field name="q" label="Cari" :value="request('q')" placeholder="Judul atau penulis" />
        </div>

        <div class="w-full sm:w-56">
            <x-field name="category" label="Kategori" type="select">
                <option value="">Semua kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((int) request('category') === $category->id)>{{ $category->name }}</option>
                @endforeach
            </x-field>
        </div>

        <div class="flex gap-2">
            <x-button type="submit">Cari</x-button>
            @if ($filtered)
                <x-button :href="route('books.index')" variant="secondary">Reset</x-button>
            @endif
        </div>
    </form>

    @if ($books->isEmpty())
        @if ($filtered)
            <x-empty-state title="Buku tidak ditemukan" message="Coba kata kunci atau kategori lain.">
                <x-button :href="route('books.index')" variant="secondary">Reset pencarian</x-button>
            </x-empty-state>
        @else
            <x-empty-state title="Belum ada buku" message="Tambahkan buku pertama ke perpustakaan.">
                <x-button :href="route('books.create')">Tambah buku</x-button>
            </x-empty-state>
        @endif
    @else
        <x-table>
            <thead>
                <tr>
                    <th>Buku</th>
                    <th>Kategori</th>
                    <th class="hidden sm:table-cell">Tahun</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($books as $book)
                    <tr>
                        <td>
                            <a href="{{ route('books.show', $book) }}" class="font-medium text-stone-900 hover:text-brand-700">{{ $book->title }}</a>
                            <p class="text-xs text-stone-500">{{ $book->author }}</p>
                        </td>
                        <td>{{ $book->category->name }}</td>
                        <td class="hidden sm:table-cell">{{ $book->year }}</td>
                        <td>
                            @if ($book->stock > 0)
                                <x-badge color="green">{{ $book->stock }}</x-badge>
                            @else
                                <x-badge color="red">Habis</x-badge>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <x-button :href="route('books.edit', $book)" variant="secondary" size="sm">Edit</x-button>

                                <form action="{{ route('books.destroy', $book) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <x-button type="submit" variant="danger" size="sm"
                                        onclick="return confirm('Hapus buku ini?')">Hapus</x-button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>

        {{ $books->links('pagination.simple') }}
    @endif
@endsection
