{{--
    Dasar semua halaman error. Sengaja TIDAK memakai auth(), sesi, atau query database,
    karena halaman ini bisa tampil justru saat database/sesi sedang bermasalah (error 500).
    Tautan "Ke beranda" mengarah ke "/", yang otomatis diteruskan ke dashboard atau login.
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('heading') | {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-stone-50 px-4 py-10 text-stone-800 antialiased">

    <main class="w-full max-w-md text-center">
        <p class="text-sm font-semibold uppercase tracking-wide text-brand-700">Kesalahan @yield('code')</p>
        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-stone-900">@yield('heading')</h1>
        <p class="mt-3 text-sm text-stone-500">@yield('message')</p>

        <div class="mt-6 flex items-center justify-center gap-2">
            <x-button :href="url('/')">Ke beranda</x-button>
            <x-button variant="secondary" onclick="history.back()">Kembali</x-button>
        </div>

        <p class="mt-10 text-xs text-stone-400">{{ config('app.name') }}</p>
    </main>

</body>
</html>
