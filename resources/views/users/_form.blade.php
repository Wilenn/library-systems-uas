{{-- Dipakai create dan edit. $user bernilai null saat membuat user baru. --}}
@php($isSelf = $user && $user->is(auth()->user()))

<div style="margin-bottom: 12px;">
    <label for="name">Nama</label><br>
    <input type="text" id="name" name="name" value="{{ old('name', $user?->name) }}">
    @error('name')
        <p style="color: red; margin: 4px 0;">{{ $message }}</p>
    @enderror
</div>

<div style="margin-bottom: 12px;">
    <label for="email">Email</label><br>
    <input type="email" id="email" name="email" value="{{ old('email', $user?->email) }}">
    @error('email')
        <p style="color: red; margin: 4px 0;">{{ $message }}</p>
    @enderror
</div>

<div style="margin-bottom: 12px;">
    <label for="role">Role</label><br>
    @if ($isSelf)
        {{-- Role akun sendiri tidak bisa diubah; dikirim lewat hidden input agar lolos validasi. --}}
        {{ ucfirst($user->role) }} (role akun sendiri tidak dapat diubah)
        <input type="hidden" name="role" value="{{ $user->role }}">
    @else
        <select name="role" id="role">
            @foreach (\App\Models\User::ROLES as $role)
                <option value="{{ $role }}" @selected(old('role', $user?->role ?? \App\Models\User::ROLE_PETUGAS) === $role)>
                    {{ ucfirst($role) }}
                </option>
            @endforeach
        </select>
    @endif
    @error('role')
        <p style="color: red; margin: 4px 0;">{{ $message }}</p>
    @enderror
</div>

<div style="margin-bottom: 12px;">
    <label for="password">Password @if ($user) (kosongkan jika tidak diubah) @endif</label><br>
    <input type="password" id="password" name="password" autocomplete="new-password">
    @error('password')
        <p style="color: red; margin: 4px 0;">{{ $message }}</p>
    @enderror
</div>

<div style="margin-bottom: 12px;">
    <label for="password_confirmation">Konfirmasi Password</label><br>
    <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
</div>

<button type="submit">Simpan</button>
<a href="{{ route('users.index') }}">Batal</a>
