{{-- Lencana status peminjaman: Terlambat / Dipinjam / Dikembalikan. --}}
@props(['borrowing'])

@if ($borrowing->isOverdue())
    <x-badge color="red">Terlambat</x-badge>
@elseif ($borrowing->isBorrowed())
    <x-badge color="amber">Dipinjam</x-badge>
@else
    <x-badge color="green">Dikembalikan</x-badge>
@endif
