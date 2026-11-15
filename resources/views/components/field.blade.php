{{--
    Satu komponen untuk semua kontrol form: label + input/select/textarea + pesan error.
    Contoh:
      <x-field name="title" label="Judul" :value="$book?->title" required />
      <x-field name="category_id" label="Kategori" type="select" required>
          <option value="1">Fiksi</option>
      </x-field>
    Nilai input otomatis memakai old() setelah validasi gagal (kecuali password).
--}}
@props(['name', 'label', 'type' => 'text', 'value' => null, 'hint' => null])

@php
    $id = $attributes->get('id', $name);
    $hasError = $errors->has($name);
    $control = 'block w-full rounded-md border bg-white px-3 py-2 text-sm text-stone-900 shadow-sm placeholder:text-stone-400 focus:outline-none focus:ring-1 disabled:bg-stone-100 '
        .($hasError
            ? 'border-red-400 focus:border-red-500 focus:ring-red-500'
            : 'border-stone-300 focus:border-brand-600 focus:ring-brand-600');
    $current = $type === 'password' ? '' : old($name, $value);
@endphp

<div>
    <label for="{{ $id }}" class="mb-1 block text-sm font-medium text-stone-700">{{ $label }}</label>

    @if ($type === 'select')
        <select id="{{ $id }}" name="{{ $name }}" {{ $attributes->except('id')->merge(['class' => $control]) }}>{{ $slot }}</select>
    @elseif ($type === 'textarea')
        <textarea id="{{ $id }}" name="{{ $name }}" rows="3" {{ $attributes->except('id')->merge(['class' => $control]) }}>{{ $current }}</textarea>
    @else
        <input type="{{ $type }}" id="{{ $id }}" name="{{ $name }}" value="{{ $current }}" {{ $attributes->except('id')->merge(['class' => $control]) }}>
    @endif

    @if ($hasError)
        <p class="mt-1 text-sm text-red-600">{{ $errors->first($name) }}</p>
    @elseif ($hint)
        <p class="mt-1 text-xs text-stone-500">{{ $hint }}</p>
    @endif
</div>
