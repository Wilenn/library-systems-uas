@extends('layouts.app')

@section('title', 'Tambah pengguna')
@section('modern', '1')

@section('content')
    <x-page-header title="Tambah pengguna" subtitle="Buat akun baru dan tentukan role-nya." />

    <x-card class="max-w-xl">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            @include('users._form', ['user' => null])
        </form>
    </x-card>
@endsection
