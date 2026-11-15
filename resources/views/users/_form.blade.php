{{-- Dipakai create dan edit. $user bernilai null saat membuat pengguna baru. --}}
@php($isSelf = $user && $user->is(auth()->user()))

<div class="space-y-4">
    <x-field name="name" label="Nama" :value="$user?->name" required />

    <x-field name="email" label="Email" type="email" :value="$user?->email" required />

    @if ($isSelf)
        {{-- Role akun sendiri tidak bisa diubah; dikirim lewat hidden input agar lolos validasi. --}}
        <div>
            <p class="mb-1 text-sm font-medium text-stone-700">Role</p>
            <p class="text-sm text-stone-600">
                {{ ucfirst($user->role) }}
                <span class="text-stone-400">(role akun sendiri tidak dapat diubah)</span>
            </p>
            <input type="hidden" name="role" value="{{ $user->role }}">
        </div>
    @else
        <x-field name="role" label="Role" type="select" required>
            @foreach (\App\Models\User::ROLES as $role)
                <option value="{{ $role }}" @selected(old('role', $user?->role ?? \App\Models\User::ROLE_PETUGAS) === $role)>
                    {{ ucfirst($role) }}
                </option>
            @endforeach
        </x-field>
    @endif

    <x-field name="password" label="Password" type="password" :required="! $user" autocomplete="new-password"
        :hint="$user ? 'Kosongkan jika tidak ingin mengubah password.' : 'Minimal 8 karakter.'" />

    <x-field name="password_confirmation" label="Konfirmasi password" type="password" autocomplete="new-password" />
</div>

<div class="mt-6 flex items-center gap-2">
    <x-button type="submit">Simpan</x-button>
    <x-button :href="route('users.index')" variant="secondary">Batal</x-button>
</div>
