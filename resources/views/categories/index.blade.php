@extends('layouts.app')

@section('content')

    <h2>Daftar Kategori</h2>

    <ul>
        @forelse($categories as $category)
            <li>{{ $category }}</li>
        @empty
            <li>Belum ada kategori.</li>
        @endforelse
    </ul>

@endsection