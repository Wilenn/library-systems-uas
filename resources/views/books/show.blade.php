@extends('layouts.app')

@section('title', $book->title)

@section('content')
    <x-page-header :title="$book->title" :subtitle="$book->author">
        <x-button :href="route('books.edit', $book)" variant="secondary">Edit</x-button>
        <x-button :href="route('books.index')" variant="ghost">Kembali</x-button>
    </x-page-header>

    <x-card class="max-w-xl">
        <dl class="grid grid-cols-3 gap-y-4 text-sm">
            <dt class="text-stone-500">Penulis</dt>
            <dd class="col-span-2 text-stone-900">{{ $book->author }}</dd>

            <dt class="text-stone-500">Kategori</dt>
            <dd class="col-span-2"><x-badge color="brand">{{ $book->category->name }}</x-badge></dd>

            <dt class="text-stone-500">Tahun terbit</dt>
            <dd class="col-span-2 text-stone-900">{{ $book->year }}</dd>

            <dt class="text-stone-500">Stok</dt>
            <dd class="col-span-2">
                @if ($book->stock > 0)
                    <x-badge color="green">{{ $book->stock }} tersedia</x-badge>
                @else
                    <x-badge color="red">Habis</x-badge>
                @endif
            </dd>
        </dl>
    </x-card>
@endsection
