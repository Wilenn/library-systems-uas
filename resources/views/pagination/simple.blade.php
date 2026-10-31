{{-- Pagination polos tanpa CSS framework (project belum memakai Tailwind/Bootstrap). --}}
@if ($paginator->hasPages())
    <p>
        @if ($paginator->onFirstPage())
            &laquo; Sebelumnya
        @else
            <a href="{{ $paginator->previousPageUrl() }}">&laquo; Sebelumnya</a>
        @endif

        | Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }} |

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}">Berikutnya &raquo;</a>
        @else
            Berikutnya &raquo;
        @endif
    </p>
@endif
