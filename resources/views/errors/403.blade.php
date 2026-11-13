@extends('errors.base')

@section('code', '403')
@section('heading', 'Akses ditolak')
{{-- Pesan 403 berasal dari kode kita sendiri (mis. middleware role), jadi aman ditampilkan. --}}
@section('message', $exception->getMessage() ?: 'Anda tidak memiliki izin untuk membuka halaman ini.')
