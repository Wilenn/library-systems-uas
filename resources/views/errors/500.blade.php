@extends('errors.base')

@section('code', '500')
@section('heading', 'Terjadi kesalahan')
{{-- Detail teknis sengaja tidak ditampilkan ke pengguna; lihat storage/logs/laravel.log. --}}
@section('message', 'Maaf, ada masalah pada server. Silakan coba lagi beberapa saat lagi.')
