{{--
    Pembungkus tabel: bisa digulir ke samping di layar kecil, dan gaya sel diatur
    di sini supaya <th>/<td> di halaman cukup ditulis polos.
    Contoh: <x-table><thead><tr><th>Nama</th></tr></thead><tbody>...</tbody></x-table>
--}}
<div class="overflow-x-auto rounded-lg border border-stone-200 bg-white shadow-sm">
    <table {{ $attributes->merge(['class' => 'min-w-full border-collapse text-left text-sm text-stone-700 [&_thead]:bg-stone-50 [&_th]:px-4 [&_th]:py-3 [&_th]:text-xs [&_th]:font-medium [&_th]:uppercase [&_th]:tracking-wide [&_th]:text-stone-500 [&_td]:px-4 [&_td]:py-3 [&_tbody_tr]:border-t [&_tbody_tr]:border-stone-100']) }}>
        {{ $slot }}
    </table>
</div>
