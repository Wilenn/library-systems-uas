@extends('layouts.app')

@section('title', 'Dashboard')
@section('modern', '1')

@section('content')
    <x-page-header title="Dashboard" :subtitle="'Halo, '.auth()->user()->name.'. Ringkasan perpustakaan hari ini.'" />

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat label="Judul buku" :value="$jumlahJudul" :href="route('books.index')" />
        <x-stat label="Total stok buku" :value="$jumlahStok" :href="route('books.index')" hint="Eksemplar yang tersedia" />
        <x-stat label="Anggota" :value="$jumlahMember" :href="route('members.index')" />
        <x-stat label="Sedang dipinjam" :value="$jumlahDipinjam" :href="route('borrowings.index', ['status' => 'borrowed'])" hint="Transaksi belum dikembalikan" />
    </div>
@endsection
