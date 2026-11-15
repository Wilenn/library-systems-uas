{{-- Dipakai create dan edit. $member bernilai null saat membuat anggota baru. --}}
<div class="space-y-4">
    <x-field name="name" label="Nama" :value="$member?->name" required autofocus />

    <x-field name="email" label="Email" type="email" :value="$member?->email" hint="Opsional." />

    <x-field name="phone" label="Nomor telepon" type="tel" :value="$member?->phone" hint="Opsional. Angka, spasi, +, -, dan tanda kurung." />

    <x-field name="address" label="Alamat" type="textarea" :value="$member?->address" hint="Opsional." />
</div>

<div class="mt-6 flex items-center gap-2">
    <x-button type="submit">Simpan</x-button>
    <x-button :href="route('members.index')" variant="secondary">Batal</x-button>
</div>
