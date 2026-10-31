@props(['variant' => 'primary', 'size' => 'md', 'href' => null, 'type' => 'button'])

@php
    $variants = [
        'primary' => 'bg-brand-700 text-white hover:bg-brand-800 focus-visible:outline-brand-700',
        'secondary' => 'border border-stone-300 bg-white text-stone-700 hover:bg-stone-50 focus-visible:outline-stone-400',
        'danger' => 'border border-red-200 bg-white text-red-700 hover:bg-red-50 focus-visible:outline-red-500',
        'ghost' => 'text-stone-600 hover:bg-stone-100 focus-visible:outline-stone-400',
    ];

    $sizes = [
        'md' => 'px-3.5 py-2 text-sm',
        'sm' => 'px-2.5 py-1.5 text-xs',
    ];

    $classes = 'inline-flex cursor-pointer items-center justify-center rounded-md font-medium transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 disabled:cursor-not-allowed disabled:opacity-60 '
        .$variants[$variant].' '.$sizes[$size];
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
