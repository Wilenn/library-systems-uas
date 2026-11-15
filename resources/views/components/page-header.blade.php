@props(['title', 'subtitle' => null])

<div class="mb-6 flex flex-wrap items-start justify-between gap-3">
    <div>
        <h1 class="text-xl font-semibold tracking-tight text-stone-900">{{ $title }}</h1>

        @if ($subtitle)
            <p class="mt-1 text-sm text-stone-500">{{ $subtitle }}</p>
        @endif
    </div>

    @if ($slot->isNotEmpty())
        <div class="flex flex-wrap items-center gap-2">{{ $slot }}</div>
    @endif
</div>
