@extends('layouts.app')

@section('title', 'Kategori')

@section('content')
    @php($canManage = auth()->user()->isAdmin())

    <x-page-header title="Kategori" subtitle="Pengelompokan buku di perpustakaan.">
        @if ($canManage)
            <x-button :href="route('categories.create')">Tambah kategori</x-button>
        @endif
    </x-page-header>

    @if ($categories->isEmpty())
        <x-empty-state title="Belum ada kategori" message="Kategori dibutuhkan sebelum buku bisa ditambahkan.">
            @if ($canManage)
                <x-button :href="route('categories.create')">Tambah kategori</x-button>
            @endif
        </x-empty-state>
    @else
        <x-table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Jumlah buku</th>
                    @if ($canManage)
                        <th>Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td class="font-medium text-stone-900">{{ $category->name }}</td>
                        <td>{{ $category->books_count }}</td>
                        @if ($canManage)
                            <td>
                                <div class="flex items-center gap-2">
                                    <x-button :href="route('categories.edit', $category)" variant="secondary" size="sm">Edit</x-button>

                                    <form action="{{ route('categories.destroy', $category) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <x-button type="submit" variant="danger" size="sm"
                                            onclick="return confirm('Hapus kategori ini?')">Hapus</x-button>
                                    </form>
                                </div>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </x-table>

        {{ $categories->links('pagination.simple') }}
    @endif
@endsection
