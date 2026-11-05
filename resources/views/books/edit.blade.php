@extends('layouts.app')

@section('title', 'Edit buku')
@section('modern', '1')

@section('content')
    <x-page-header title="Edit buku" :subtitle="$book->title" />

    <x-card class="max-w-xl">
        <form action="{{ route('books.update', $book) }}" method="POST">
            @csrf
            @method('PUT')
            @include('books._form')
        </form>
    </x-card>
@endsection
