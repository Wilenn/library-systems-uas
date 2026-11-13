{{-- Kartu statistik: label kecil, angka besar. Jadi tautan bila $href diberikan. --}}
@props(['label', 'value', 'href' => null, 'hint' => null])

@php
    $tag = $href ? 'a' : 'div';
    $classes = 'block rounded-lg border border-stone-200 bg-white p-5 shadow-sm'.($href ? ' transition-colors hover:border-brand-600' : '');
@endphp

<{{ $tag }} @if ($href) href="{{ $href }}" @endif {{ $attributes->merge(['class' => $classes]) }}>
    <p class="text-sm text-stone-500">{{ $label }}</p>
    <p class="mt-2 text-3xl font-semibold tracking-tight text-stone-900">{{ number_format($value, 0, ',', '.') }}</p>

    @if ($hint)
        <p class="mt-1 text-xs text-stone-500">{{ $hint }}</p>
    @endif
</{{ $tag }}>
