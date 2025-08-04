@extends('layouts.main')
@section('content')
{{-- <header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <h1 class="text-xl font-bold tracking-tight text-gray-900">App</h1>
    </div>
  </header> --}}
<main>
    <x-errors-any></x-errors-any>
    <x-validation-feedback></x-validation-feedback>
    {{-- SEARCH / FILTER --}}
    <div class="relative rounded mt-9">
        <div class="flex absolute -top-6 left-1/2 -translate-x-1/2 z-20">
            @foreach ($pembelian_menus as $key_pembelian_menu => $pembelian_menu)
            @if ($route_now === $pembelian_menu['route'])
            @if ($key_pembelian_menu !== 0)
            <div class="border rounded-t-lg bg-white px-1 border-b-4 font-bold ml-2">{{ $pembelian_menu['name'] }}</div>
            @else
            <div class="border rounded-t-lg bg-white px-1 border-b-4 font-bold">{{ $pembelian_menu['name'] }}</div>
            @endif
            @else
            @if ($key_pembelian_menu !== 0)
            <a href="{{ route($pembelian_menu['route']) }}" class="border rounded-t-lg bg-white px-1 hover:bg-slate-100 ml-2">{{ $pembelian_menu['name'] }}</a>
            @else
            <a href="{{ route($pembelian_menu['route']) }}" class="border rounded-t-lg bg-white px-1 hover:bg-slate-100">{{ $pembelian_menu['name'] }}</a>
            @endif
            @endif
            @endforeach
        </div>
        <div class="relative bg-white border-t z-10">
            <div class="mx-1 py-1 sm:px-6 lg:px-8 text-xs mt-1">
                <div class="flex">
                    <button id="filter" class="border rounded border-yellow-500 text-yellow-500 px-3 py-1" onclick="toggle_light(this.id,'filter-content',[],['bg-yellow-200'], 'block')">Filter</button>
                    <button type="submit" class="border rounded border-indigo-300 text-indigo-500 font-semibold px-3 py-1 ml-1" id="btn_new_barang" onclick="toggle_light(this.id, 'form_new_barang', [], ['bg-indigo-200'], 'block')">+ Barang</button>
                </div>
                <div class="hidden" id="filter-content">
                    <div class="rounded p-2 bg-white shadow drop-shadow inline-block mt-1">
                        <form action="" method="GET">
                            <div class="flex items-end">
                                <div>
                                    <div>Supplier :</div>
                                    <div class="mt-1">
                                        <input type="text" class="border rounded text-xs p-1" name="supplier_nama" placeholder="Nama Supplier..." id="supplier_nama">
                                        <input type="hidden" name="supplier_id" id="supplier_id">
                                    </div>
                                </div>
                                <div class="ml-2">
                                    <div>Barang :</div>
                                    <div class="mt-1">
                                        <input type="text" class="w-60 border rounded text-xs p-1" name="barang_nama" placeholder="Nama Barang..." id="barang_nama">
                                        <input type="hidden" name="barang_id" id="barang_id">
                                    </div>
                                </div>
                                <div class="ml-2">
                                    <button type="submit" class="flex items-center bg-orange-500 text-white py-1 px-3 rounded hover:bg-orange-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                        </svg>
                                        <span class="ml-1">Search</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            {{-- END - SEARCH / FILTER --}}
            {{-- FORM_NEW_BARANG --}}
            <div id="form_new_barang" class="hidden">
                <div class="flex justify-center">
                    <form action="{{ route('barangs.store') }}" method="POST" class="border rounded border-indigo-300 p-1 mt-1 lg:w-3/5 md:w-3/4">
                        @csrf
                        <table class="text-xs w-full">
                            <tr>
                                <td>Supplier</td><td><div class="mx-2">:</div></td>
                                <td class="py-1">
                                    <input type="text" name="supplier_nama" id="barang_new-supplier_nama" placeholder="nama supplier..." class="text-xs rounded-md border-0 py-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600">
                                    <input type="hidden" name="supplier_id" id="barang_new-supplier_id">
                                </td>
                            </tr>
                            <tr>
                                <td>Nama</td><td><div class="mx-2">:</div></td>
                                <td>
                                    <input type="text" name="barang_nama" id="barang_new-barang_nama" placeholder="nama barang ..." class="w-full text-xs rounded-md border-0 py-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600">
                                    <input type="hidden" name="barang_id" id="barang_new-barang_id">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <div class="my-5 border rounded p-1 border-sky-500">
                                        <div class="my-2 font-semibold text-center">Satuan - Jumlah - Harga per Satuan - Harga Total:</div>
                                        <table class="w-full">
                                            <tr>
                                                <td>Satuan Utama</td><td><div class="mx-1">:</div></td><td><input type="text" name="satuan_main" class="text-xs rounded p-1 w-3/4"></td>
                                                <td>Jumlah</td><td><div class="mx-1">:</div></td>
                                                <td>
                                                    <input type="text" name="jumlah_main" id="barang_new-jumlah_main" class="text-xs rounded p-1 w-3/4" oninput="count_harga_total_main()">
                                                </td>
                                                <td>Harga</td><td><div class="mx-1">:</div></td>
                                                <td>
                                                    <input type="text" id="barang_new-harga_main" class="text-xs rounded p-1" onchange="formatNumber(this, 'barang_new-harga_main-real');count_harga_total_main()">
                                                    <input type="hidden" name="harga_main" id="barang_new-harga_main-real">
                                                </td>
                                                <td>Harga Total</td><td><div class="mx-1">:</div></td>
                                                <td>
                                                    <input type="text" name="harga_total_main" id="barang_new-harga_total_main" class="text-xs rounded p-1" onchange="formatNumber(this, 'barang_new-harga_total_main-real');copy_to_harga_sub();count_harga_total_sub()">
                                                    <input type="hidden" name="harga_total_main" id="barang_new-harga_total_main-real">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Satuan Sub</td><td><div class="mx-1">:</div></td><td><input type="text" name="satuan_sub" class="text-xs rounded p-1 w-3/4"></td>
                                                <td>Jumlah</td><td><div class="mx-1">:</div></td>
                                                <td>
                                                    <input type="text" name="jumlah_sub" id="barang_new-jumlah_sub" class="text-xs rounded p-1 w-3/4" oninput="count_harga_total_sub()">
                                                </td>
                                                <td>Harga</td><td><div class="mx-1">:</div></td>
                                                <td>
                                                    <input type="text" id="barang_new-harga_sub" class="text-xs rounded p-1" onchange="formatNumber(this, 'barang_new-harga_sub-real');count_harga_total_sub()">
                                                    <input type="hidden" name="harga_sub" id="barang_new-harga_sub-real">
                                                </td>
                                                <td>Harga Total</td><td><div class="mx-1">:</div></td>
                                                <td>
                                                    <input type="text" name="harga_total_sub" id="barang_new-harga_total_sub" class="text-xs rounded p-1" onchange="formatNumber(this, 'barang_new-harga_total_sub-real');">
                                                    <input type="hidden" name="harga_total_sub" id="barang_new-harga_total_sub-real">
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                            <tr class="align-top">
                                <td>Ket. (opt.)</td><td><div class="mx-2">:</div></td>
                                <td class="py-1"><textarea name="keterangan" id="" cols="40" rows="3" placeholder="keterangan..." class="rounded text-xs p-1"></textarea></td>
                            </tr>
                        </table>
                        <div class="flex justify-center mt-3">
                            <button type="submit" class="border-2 border-indigo-300 bg-indigo-200 text-indigo-600 rounded-lg font-semibold py-1 px-3 hover:bg-indigo-300">Tambah Barang</button>
                        </div>
                    </form>
                </div>
            </div>
            {{-- END - FORM_NEW_BARANG --}}
            <div class="flex justify-center">
                <div class='pb-1 text-xs lg:w-1/2 md:w-3/4'>
                    <table class="table-nice w-full">
                        @for ($i = 0; $i < count($suppliers); $i++)
                        <tr><td><div class="font-bold text-slate-500">{{ $suppliers[$i]->nama }}</div></td></tr>
                        @for ($j = 0; $j < count($barangs[$i]); $j++)
                        <tr class="border-b">
                            <td>
                                <a href="{{ route('barangs.show', $barangs[$i][$j]->id) }}" class="text-sky-500">
                                    <div class="min-w-max">
                                        {{ $barangs[$i][$j]['nama'] }}
                                    </div>
                                </a>
                            </td>
                            <td>{{ $barangs[$i][$j]->jumlah_main / 100 }} {{ $barangs[$i][$j]->satuan_main }}</td>
                            <td>
                                <div class="flex justify-between font-semibold">
                                    <span>Rp</span>
                                    {{ number_format($barangs[$i][$j]->harga_main,2,',','.') }}
                                </div>
                            </td>
                            <td>
                                <div class="flex justify-between font-semibold">
                                    <span>Rp</span>
                                    {{ number_format($barangs[$i][$j]->harga_total_main,2,',','.') }}
                                </div>
                            </td>
                            <td>
                                <button id="btn_detail_barang-{{ $i }}-{{ $j }}" class="border rounded" onclick="showDropdown(this.id, 'detail_barang-{{ $i }}-{{ $j }}')">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        <tr class="hidden" id="detail_barang-{{ $i }}-{{ $j }}">
                            <td colspan="5">
                                <table>
                                    <tr>
                                        <td>Satuan Utama</td><td>:</td><td>{{ $barangs[$i][$j]->satuan_main }}</td>
                                        <td>Jumlah</td><td>:</td><td>{{ $barangs[$i][$j]->jumlah_main / 100 }}</td>
                                        <td>Harga</td><td>:</td><td>{{ number_format($barangs[$i][$j]->harga_main,2,',','.') }}</td>
                                        <td>Total</td><td>:</td><td>{{ number_format($barangs[$i][$j]->harga_total_main,2,',','.') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Satuan Sub</td><td>:</td><td>@if ($barangs[$i][$j]->satuan_sub){{ $barangs[$i][$j]->satuan_sub }}@else-@endif</td>
                                        <td>Jumlah</td><td>:</td>
                                        <td>@if ($barangs[$i][$j]->jumlah_sub){{ $barangs[$i][$j]->jumlah_sub / 100 }}@else-@endif</td>
                                        <td>Harga</td><td>:</td>
                                        <td>@if ($barangs[$i][$j]->harga_sub){{ number_format($barangs[$i][$j]->harga_sub,2,',','.') }}@else-@endif</td>
                                        <td>Total</td><td>:</td><td>@if ($barangs[$i][$j]->harga_total_sub){{ number_format($barangs[$i][$j]->harga_total_sub,2,',','.') }}@else-@endif</td>
                                    </tr>
                                </table>
                            </td>
                            <td class="align-bottom">
                                <div>
                                    <a href="{{ route('barangs.edit', $barangs[$i][$j]->id) }}">
                                        <button class="rounded bg-slate-200 text-slate-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                        </button>
                                    </a>
                                </div>
                                <form action="{{ route('barangs.delete', $barangs[$i][$j]->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus barang ini?')">
                                    @csrf
                                    <button type="submit" class="text-red-500 bg-red-200 rounded">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endfor
                        @endfor
                    </table>
                </div>
            </div>
            <div class="w-56"></div>
        </div>
    </div>
</main>

<script>
    const label_supplier = {!! json_encode($label_supplier, JSON_HEX_TAG) !!}

    $("#supplier_nama").autocomplete({
        source: label_supplier,
        select: function(event, ui) {
            $("#supplier_id").val(ui.item.id);
        }
    });

    $("#barang_new-supplier_nama").autocomplete({
        source: label_supplier,
        select: function(event, ui) {
            $("#barang_new-supplier_id").val(ui.item.id);
        }
    });

    const label_barang = {!! json_encode($label_barang, JSON_HEX_TAG) !!};
    // console.log(label_barang);
    // function set_autocomplete_item(input_id, label_id, value_id, satuan_sub_id, satuan_main_class, jumlah_sub_id, jumlah_main_id, harga_main_id, harga_t_id) {

    $("#barang_nama").autocomplete({
        source: label_barang,
        select: function(event, ui) {
            $("#barang_nama").val(ui.item.value);
            $("#barang_id").val(ui.item.id);
        }
    });

    $("#barang_new-barang_nama").autocomplete({
        source: label_barang,
        select: function(event, ui) {
            $("#barang_new-barang_id").val(ui.item.id);
        }
    });

    // FUNGSI BARANG
    function count_harga_total_main() {
        let harga_main = document.getElementById('barang_new-harga_main-real').value;
        let jumlah_main = document.getElementById('barang_new-jumlah_main').value;
        let harga_total_main_el = document.getElementById('barang_new-harga_total_main');

        let harga_total_main = 0;
        if (jumlah_main !== '' && harga_main !== '') {
            harga_total_main = jumlah_main * harga_main;
            harga_total_main_el.value = harga_total_main;
            formatNumber(harga_total_main_el, 'barang_new-harga_total_main-real');
            let harga_sub = document.getElementById('barang_new-harga_sub');
            harga_sub.value = harga_total_main;
            formatNumber(harga_sub, 'barang_new-harga_sub-real');
        }
    }

    function copy_to_harga_sub() {
        let harga_total_main_real = document.getElementById('barang_new-harga_total_main-real');
        let harga_sub = document.getElementById('barang_new-harga_sub');

        harga_sub.value = harga_total_main_real.value;
        formatNumber(harga_sub, 'barang_new-harga_sub-real')
    }

    function count_harga_total_sub() {
        let harga_sub = document.getElementById('barang_new-harga_sub-real').value;
        let jumlah_sub = document.getElementById('barang_new-jumlah_sub').value;
        let harga_total_sub_el = document.getElementById('barang_new-harga_total_sub');

        let harga_total_sub = 0;
        if (jumlah_sub !== '' && harga_sub !== '') {
            harga_total_sub = jumlah_sub * harga_sub;
            harga_total_sub_el.value = harga_total_sub;
            formatNumber(harga_total_sub_el, 'barang_new-harga_total_sub-real');
        }
    }
    // END - FUNGSI BARANG
</script>

@endsection
