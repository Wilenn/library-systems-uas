@props(['color' => 'neutral'])

@php
    $colors = [
        'neutral' => 'bg-stone-100 text-stone-700',
        'brand' => 'bg-brand-100 text-brand-800',
        'green' => 'bg-green-100 text-green-800',
        'amber' => 'bg-amber-100 text-amber-800',
        'red' => 'bg-red-100 text-red-800',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium '.$colors[$color]]) }}>{{ $slot }}</span>
