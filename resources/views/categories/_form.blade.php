{{-- Dipakai create dan edit. $category bernilai null saat membuat kategori baru. --}}
<x-field name="name" label="Nama kategori" :value="$category?->name" required autofocus />

<div class="mt-6 flex items-center gap-2">
    <x-button type="submit">Simpan</x-button>
    <x-button :href="route('categories.index')" variant="secondary">Batal</x-button>
</div>
