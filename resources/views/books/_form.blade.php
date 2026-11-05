{{-- Dipakai create dan edit. $book bernilai null saat membuat buku baru. --}}
@if ($categories->isEmpty())
    <x-alert type="info" class="mb-4">
        Belum ada kategori.
        @if (auth()->user()->isAdmin())
            <a href="{{ route('categories.create') }}" class="font-medium underline">Tambah kategori</a> terlebih dahulu.
        @else
            Hubungi admin untuk menambahkan kategori.
        @endif
    </x-alert>
@endif

<div class="space-y-4">
    <x-field name="title" label="Judul" :value="$book?->title" required autofocus />

    <x-field name="author" label="Penulis" :value="$book?->author" required />

    <div class="grid gap-4 sm:grid-cols-2">
        <x-field name="year" label="Tahun terbit" type="number" :value="$book?->year" min="1901" :max="date('Y')" required />
        <x-field name="stock" label="Stok" type="number" :value="$book?->stock ?? 1" min="0" max="1000" required />
    </div>

    <x-field name="category_id" label="Kategori" type="select" required>
        <option value="">Pilih kategori</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected((int) old('category_id', $book?->category_id) === $category->id)>{{ $category->name }}</option>
        @endforeach
    </x-field>
</div>

<div class="mt-6 flex items-center gap-2">
    <x-button type="submit">Simpan</x-button>
    <x-button :href="route('books.index')" variant="secondary">Batal</x-button>
</div>
