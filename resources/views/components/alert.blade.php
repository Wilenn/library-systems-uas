@props(['type' => 'success'])

@php
    $styles = [
        'success' => 'border-green-200 bg-green-50 text-green-800',
        'error' => 'border-red-200 bg-red-50 text-red-800',
        'info' => 'border-stone-200 bg-stone-100 text-stone-700',
    ];
@endphp

<div role="{{ $type === 'error' ? 'alert' : 'status' }}" {{ $attributes->merge(['class' => 'rounded-md border px-4 py-3 text-sm '.$styles[$type]]) }}>
    {{ $slot }}
</div>
