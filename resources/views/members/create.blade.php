@extends('layouts.app')

@section('title', 'Tambah anggota')

@section('content')
    <x-page-header title="Tambah anggota" subtitle="Daftarkan anggota baru perpustakaan." />

    <x-card class="max-w-xl">
        <form action="{{ route('members.store') }}" method="POST">
            @csrf
            @include('members._form', ['member' => null])
        </form>
    </x-card>
@endsection
