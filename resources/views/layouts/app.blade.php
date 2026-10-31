<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@hasSection('title')@yield('title') | @endif{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-50 text-stone-800 antialiased">

    {{-- Top bar: hanya tampil di layar kecil --}}
    <header class="sticky top-0 z-30 flex items-center justify-between border-b border-stone-200 bg-white px-4 py-3 lg:hidden">
        <a href="{{ route('dashboard') }}" class="font-semibold tracking-tight text-brand-800">{{ config('app.name') }}</a>
        <button type="button" id="menu-toggle" aria-controls="sidebar" aria-expanded="false"
            class="rounded-md border border-stone-300 px-3 py-1.5 text-sm font-medium text-stone-700 hover:bg-stone-50">
            Menu
        </button>
    </header>

    {{-- Sidebar: tetap di desktop, dibuka-tutup lewat tombol Menu di layar kecil --}}
    <aside id="sidebar"
        class="flex flex-col border-b border-stone-200 bg-white max-lg:hidden lg:fixed lg:inset-y-0 lg:left-0 lg:w-64 lg:border-r lg:border-b-0">
        <a href="{{ route('dashboard') }}" class="hidden px-6 py-5 text-lg font-semibold tracking-tight text-brand-800 lg:block">
            {{ config('app.name') }}
        </a>

        <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-3" aria-label="Menu utama">
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Dashboard</x-nav-link>
            <x-nav-link :href="route('books.index')" :active="request()->routeIs('books.*')">Buku</x-nav-link>
            <x-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">Kategori</x-nav-link>
            <x-nav-link :href="route('members.index')" :active="request()->routeIs('members.*')">Anggota</x-nav-link>

            @if (auth()->user()->isAdmin())
                <p class="px-3 pb-1 pt-5 text-xs font-medium uppercase tracking-wide text-stone-400">Administrasi</p>
                <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">Pengguna</x-nav-link>
            @endif
        </nav>

        <div class="border-t border-stone-200 p-4">
            <p class="truncate text-sm font-medium text-stone-900">{{ auth()->user()->name }}</p>
            <p class="mb-3 text-xs text-stone-500">{{ ucfirst(auth()->user()->role) }}</p>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <x-button type="submit" variant="secondary" size="sm" class="w-full">Keluar</x-button>
            </form>
        </div>
    </aside>

    <div class="lg:pl-64">
        <main class="mx-auto w-full max-w-5xl px-4 py-6 sm:px-6 lg:px-8 @hasSection('modern') @else legacy @endif">
            @if (session('success'))
                <x-alert class="mb-4" data-autodismiss>{{ session('success') }}</x-alert>
            @endif

            @if (session('error'))
                <x-alert type="error" class="mb-4">{{ session('error') }}</x-alert>
            @endif

            @yield('content')
        </main>
    </div>

</body>
</html>
