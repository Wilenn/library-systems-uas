@props(['active' => false])

<a {{ $attributes->class([
    'block rounded-md px-3 py-2 text-sm font-medium transition-colors',
    'bg-brand-50 text-brand-800' => $active,
    'text-stone-600 hover:bg-stone-100 hover:text-stone-900' => ! $active,
]) }} @if ($active) aria-current="page" @endif>{{ $slot }}</a>
