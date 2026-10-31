@extends('layouts.app')

@section('content')
    <h2>Edit Buku</h2>

    <form action="{{ route('books.update', $book) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="margin-bottom: 12px;">
            <label for="title">Judul Buku</label><br>
            <input type="text" id="title" name="title" value="{{ old('title', $book->title) }}">
            @error('title')
                <p style="color: red; margin: 4px 0;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 12px;">
            <label for="author">Penulis</label><br>
            <input type="text" id="author" name="author" value="{{ old('author', $book->author) }}">
            @error('author')
                <p style="color: red; margin: 4px 0;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 12px;">
            <label for="category_id">Kategori</label><br>
            <select name="category_id" id="category_id">
                <option value="">Pilih kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $book->category_id) == $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <p style="color: red; margin: 4px 0;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 12px;">
            <label for="year">Tahun Terbit</label><br>
            <input type="text" id="year" name="year" value="{{ old('year', $book->year) }}">
            @error('year')
                <p style="color: red; margin: 4px 0;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 12px;">
            <label for="stock">Stok</label><br>
            <input type="text" id="stock" name="stock" value="{{ old('stock', $book->stock) }}">
            @error('stock')
                <p style="color: red; margin: 4px 0;">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit">Update</button>
        <a href="{{ route('books.index') }}">Batal</a>
    </form>
@endsection
