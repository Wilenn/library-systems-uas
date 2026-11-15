<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@hasSection('title')@yield('title') | @endif{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-stone-50 px-4 py-10 text-stone-800 antialiased">

    <div class="w-full max-w-sm">
        <p class="mb-6 text-center text-lg font-semibold tracking-tight text-brand-800">{{ config('app.name') }}</p>

        <x-card class="p-6">
            @yield('content')
        </x-card>
    </div>

</body>
</html>
