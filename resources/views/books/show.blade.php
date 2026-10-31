@extends('layouts.app')

@section('content')

    <h2>Detail Buku</h2>

    <p>ID: {{ $book->id }}</p>
    <p>Judul: {{ $book->title }}</p>
    <p>Penulis: {{ $book->author }}</p>
    <p>Kategori: {{ $book->category?->name ?? 'Belum ada kategori' }}</p>
    <p>Tahun: {{ $book->year }}</p>
    <p>Stok: {{ $book->stock }}</p>

    <a href="{{ route('books.index') }}">Kembali</a>

@endsection