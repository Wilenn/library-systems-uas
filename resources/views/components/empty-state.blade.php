@props(['title', 'message' => null])

<div {{ $attributes->merge(['class' => 'rounded-lg border border-dashed border-stone-300 bg-white px-6 py-12 text-center']) }}>
    <p class="text-sm font-medium text-stone-900">{{ $title }}</p>

    @if ($message)
        <p class="mt-1 text-sm text-stone-500">{{ $message }}</p>
    @endif

    @if ($slot->isNotEmpty())
        <div class="mt-4">{{ $slot }}</div>
    @endif
</div>
