{{--
    Satu baris transaksi pada daftar ringkas (dashboard). Dipakai bersama oleh daftar
    "terlambat" dan "terbaru" agar format keterangannya selalu sama.
    Isi slot = lencana di sisi kanan.
--}}
@props(['borrowing'])

<li>
    <a href="{{ route('borrowings.show', $borrowing) }}"
        class="flex items-center justify-between gap-3 px-5 py-3 transition-colors hover:bg-stone-50">
        <span class="min-w-0">
            <span class="block truncate text-sm font-medium text-stone-900">{{ $borrowing->member->name }}</span>
            <span class="block text-xs text-stone-500">
                Jatuh tempo {{ $borrowing->due_date->format('d/m/Y') }} · {{ $borrowing->books_count }} buku
            </span>
        </span>

        {{ $slot }}
    </a>
</li>
