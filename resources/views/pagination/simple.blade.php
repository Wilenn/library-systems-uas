@if ($paginator->hasPages())
    <nav class="mt-4 flex items-center justify-between gap-3 text-sm text-stone-600" aria-label="Navigasi halaman">
        @if ($paginator->onFirstPage())
            <span class="rounded-md border border-stone-200 px-2.5 py-1.5 text-xs font-medium text-stone-400">&laquo; Sebelumnya</span>
        @else
            <x-button :href="$paginator->previousPageUrl()" variant="secondary" size="sm">&laquo; Sebelumnya</x-button>
        @endif

        <span>Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}</span>

        @if ($paginator->hasMorePages())
            <x-button :href="$paginator->nextPageUrl()" variant="secondary" size="sm">Berikutnya &raquo;</x-button>
        @else
            <span class="rounded-md border border-stone-200 px-2.5 py-1.5 text-xs font-medium text-stone-400">Berikutnya &raquo;</span>
        @endif
    </nav>
@endif
