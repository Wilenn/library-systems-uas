@extends('layouts.app')

@section('title', 'Tambah buku')
@section('modern', '1')

@section('content')
    <x-page-header title="Tambah buku" subtitle="Isi data buku baru dan tentukan kategorinya." />

    <x-card class="max-w-xl">
        <form action="{{ route('books.store') }}" method="POST">
            @csrf
            @include('books._form', ['book' => null])
        </form>
    </x-card>
@endsection
