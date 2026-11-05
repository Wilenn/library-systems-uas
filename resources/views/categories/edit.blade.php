@extends('layouts.app')

@section('title', 'Edit kategori')
@section('modern', '1')

@section('content')
    <x-page-header title="Edit kategori" :subtitle="$category->name" />

    <x-card class="max-w-xl">
        <form action="{{ route('categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')
            @include('categories._form')
        </form>
    </x-card>
@endsection
