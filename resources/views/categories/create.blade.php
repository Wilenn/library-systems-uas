@extends('layouts.app')

@section('title', 'Tambah kategori')

@section('content')
    <x-page-header title="Tambah kategori" />

    <x-card class="max-w-xl">
        <form action="{{ route('categories.store') }}" method="POST">
            @csrf
            @include('categories._form', ['category' => null])
        </form>
    </x-card>
@endsection
