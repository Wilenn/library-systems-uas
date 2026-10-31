@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
    <h1 class="text-lg font-semibold text-stone-900">Masuk</h1>
    <p class="mb-5 mt-1 text-sm text-stone-500">Silakan masuk untuk mengelola perpustakaan.</p>

    @if (session('success'))
        <x-alert class="mb-4" data-autodismiss>{{ session('success') }}</x-alert>
    @endif

    <form action="{{ route('login.submit') }}" method="POST" class="space-y-4">
        @csrf

        <x-field name="email" label="Email" type="email" required autofocus autocomplete="username" />
        <x-field name="password" label="Password" type="password" required autocomplete="current-password" />

        <x-button type="submit" class="w-full">Masuk</x-button>
    </form>
@endsection
