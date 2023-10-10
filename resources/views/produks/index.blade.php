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
            @foreach ($spk_menus as $key_spk_menu => $spk_menu)
            @if ($route_now === $spk_menu['route'])
            @if ($key_spk_menu !== 0)
            <div class="border rounded-t-lg bg-white px-1 border-b-4 font-bold ml-2">{{ $spk_menu['name'] }}</div>
            @else
            <div class="border rounded-t-lg bg-white px-1 border-b-4 font-bold">{{ $spk_menu['name'] }}</div>
            @endif
            @else
            @if ($key_spk_menu !== 0)
            <a href="{{ route($spk_menu['route']) }}" class="border rounded-t-lg bg-white px-1 hover:bg-slate-100 ml-2">{{ $spk_menu['name'] }}</a>
            @else
            <a href="{{ route($spk_menu['route']) }}" class="border rounded-t-lg bg-white px-1 hover:bg-slate-100">{{ $spk_menu['name'] }}</a>
            @endif
            @endif
            @endforeach
        </div>
        <div class="relative bg-white border-t z-10">
            <div class="mx-1 py-1 sm:px-6 lg:px-8 text-xs flex items-end">
                <div id="filter-content">
                    <div class="rounded p-2 bg-white shadow drop-shadow inline-block mt-1">
                        <form action="" method="GET">
                            <div class="ml-1 mt-2 flex items-end">
                                <div>
                                    <label>Supplier:</label>
                                    <div class="flex mt-1">
                                        <input type="text" class="border rounded text-xs p-1" name="supplier_nama" placeholder="Nama Supplier..." id="supplier_nama">
                                        <input type="hidden" name="supplier_id" id="supplier_id">
                                    </div>
                                </div>
                                <div class="ml-2">
                                    <label>Nama Produk:</label>
                                    <div class="flex mt-1">
                                        <input type="text" class="border rounded text-xs p-1" name="produk_nama" placeholder="Nama Produk..." id="produk_nama">
                                        <input type="hidden" name="produk_id" id="produk_id">
                                    </div>
                                </div>
                                <div>
                                    <button type="submit" class="ml-2 flex items-center bg-yellow-500 text-white py-1 px-3 rounded hover:bg-yellow-700">
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
                <div>
                    {{-- <button id="filter" class="border rounded border-yellow-500 text-yellow-500 px-3 py-1" onclick="toggle_light(this.id,'filter-content',[],['bg-yellow-200'], 'block')">Filter</button> --}}
                    <button type="submit" class="border rounded border-emerald-300 text-emerald-500 font-semibold px-3 py-1 ml-1" id="btn_new_produk" onclick="toggle_light(this.id, 'form_new_produk', [], ['bg-emerald-200'], 'block')">+ Produk</button>
                    {{-- <button type="submit" class="border rounded border-indigo-300 text-indigo-500 font-semibold px-3 py-1 ml-1" id="btn_new_barang" onclick="toggle_light(this.id, 'form_new_barang', [], ['bg-indigo-200'], 'block')">+ Barang</button> --}}
                </div>
                <div class="ml-2 text-slate-500">Jumlah Produk: {{ $jumlah_produk }}</div>
            </div>
            {{-- END - SEARCH / FILTER --}}
            {{-- FORM NEW_PRODUK --}}
            <div id="form_new_produk" class="hidden">
                <div class="flex justify-center">
                    <form action="{{ route('produks.store') }}" method="POST" class="border rounded border-emerald-300 p-1 mt-1 lg:w-3/5 md:w-3/4">
                        @csrf
                        <div class="border rounded p-2">
                            <div>
                                <table class="text-xs w-full">
                                    <tr>
                                        <td>Supplier</td><td><div class="mx-2">:</div></td>
                                        <td class="py-1">
                                            <input type="text" name="supplier_nama" id="produk_new-supplier_nama" value="{{ old('supplier_nama') }}" placeholder="nama supplier..." class="text-xs rounded-md border-0 py-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600">
                                            <input type="hidden" name="supplier_id" id="produk_new-supplier_id" value="{{ old('supplier_id') }}">
                                        </td>
                                        <td>Tipe Produk</td><td><div class="mx-2">:</div></td>
                                        <td>
                                            <select name="tipe" id="tipe" class="text-xs py-0 rounded">
                                                @foreach ($types as $tipe)
                                                <option value="{{ $tipe['tipe'] }}">{{ $tipe['tipe'] }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Nama</td><td><div class="mx-2">:</div></td>
                                        <td><input type="text" name="nama" value="{{ old('nama') }}" id="produk_new-produk_nama" class="rounded p-1 text-xs w-full" oninput="generate_nama_nota(this.value)" placeholder="Nama Produk ..."></td>
                                        <td>Harga</td><td><div class="mx-2">:</div></td>
                                        <td>
                                            <input type="text" name="harga_formatted" value="{{ old('harga_formatted') }}" id="produk_new-harga" class="rounded p-1 text-xs" onchange="formatNumber(this, 'produk_new-harga_real')" placeholder="Harga ...">
                                            <input type="hidden" name="harga" id="produk_new-harga_real" value="{{ old('harga') }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Nama Nota</td><td><div class="mx-2">:</div></td><td><input type="text" name="nama_nota" value="{{ old('nama_nota') }}" id="nama_nota" class="rounded p-1 text-xs w-full" placeholder="Nama Nota ..."></td>
                                        <td>Tipe Packing</td><td><div class="mx-2">:</div></td>
                                        <td>
                                            <select name="tipe_packing" id="tipe_packing" class="text-xs py-0 rounded">
                                                @if (old('tipe_packing'))
                                                @foreach ($tipe_packing as $tp)
                                                @if ($tp === old('tipe_packing'))
                                                <option value="{{ $tp }}" selected>{{ $tp }}</option>
                                                @else
                                                <option value="{{ $tp }}">{{ $tp }}</option>
                                                @endif
                                                @endforeach
                                                @else
                                                @foreach ($tipe_packing as $tp)
                                                @if ($tp === 'colly')
                                                <option value="{{ $tp }}" selected>{{ $tp }}</option>
                                                @else
                                                <option value="{{ $tp }}">{{ $tp }}</option>
                                                @endif
                                                @endforeach
                                                @endif
                                            </select>
                                        </td>
                                    </tr>
                                    <tr class="align-top">
                                        <td>Ket. (opt.)</td><td><div class="mx-2">:</div></td>
                                        <td class="py-1">
                                            {{-- <input type="text" name="keterangan" placeholder="judul/keterangan..." class="text-xs rounded-md border-0 py-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600"> --}}
                                            <textarea name="keterangan" id="" cols="30" rows="5" class="border rounded p-1 text-xs" placeholder="keterangan (opt.)">{{ old('keterangan') }}</textarea>
                                        </td>
                                        <td>Aturan Packing</td><td><div class="mx-2">:</div></td><td><input type="number" name="aturan_packing" value="{{ old('aturan_packing') }}" id="produk_new-aturan_packing" class="rounded p-1 text-xs" placeholder="Aturan Packing ..."></td>

                                    </tr>
                                </table>
                            </div>

                        </div>
                        <div class="flex justify-center mt-3">
                            <button type="submit" class="border-2 border-emerald-300 bg-emerald-200 text-emerald-600 rounded-lg font-semibold px-3 hover:bg-emerald-300">+ Produk</button>
                        </div>
                    </form>
                </div>
            </div>
            {{-- END - FORM NEW_PRODUK --}}

            <div class="flex justify-center mt-1">
                <div class='pb-1 text-xs lg:w-1/2 md:w-3/4'>
                    <div class="flex">
                        <button id="btn_all" class="btn-tipe border border-violet-300 text-violet-500 px-1 rounded ml-1" onclick="toggle_light_class(this.id, 'btn-tipe', 'produk-tipe', 'all', [], ['bg-violet-200'])">all</button>
                        @foreach ($types as $key_tipe => $tipe)
                        <button id="btn_{{ $tipe['initial'] }}" class="btn-tipe border border-violet-300 text-violet-500 px-1 rounded ml-1" onclick="toggle_light_class(this.id, 'btn-tipe', 'produk-tipe', '{{ $tipe['initial'] }}', [], ['bg-violet-200'])">{{ $tipe['initial'] }}</button>
                        @endforeach
                    </div>

                    @for ($i = 0; $i < count($types); $i++)
                    {{-- TABLE PRODUKS --}}
                    <table id="{{ $types[$i]['initial'] }}" class="produk-tipe table-nice w-full mt-1">
                        <tr>
                            <td class="font-bold">{{ $types[$i]['tipe'] }}</td>
                        </tr>
                        @for ($j = 0; $j < count($produks[$i]); $j++)
                        <tr class="border-b">
                            <td>
                                <a href="{{ route('produks.show', $produks[$i][$j]->id) }}" class="text-sky-500">
                                    <div class="min-w-max">
                                        {{ $produks[$i][$j]['nama'] }}
                                    </div>
                                </a>
                            </td>
                            <td>
                                @if ($produks[$i][$j]->supplier_nama !== null)
                                @if ($produks[$i][$j]->supplier_id !== null)
                                <a href="{{ route('suppliers.show', $produks[$i][$j]->supplier_id) }}" class="text-indigo-500">{{ $produks[$i][$j]->supplier_nama }}</a>
                                @else
                                <span>{{ $produks[$i][$j]->supplier_nama }}</span>
                                @endif
                                @else
                                --
                                @endif
                            </td>
                            <td>
                                @if ($hargas[$i][$j])
                                <div class="flex justify-between font-semibold">
                                    <span>Rp</span>
                                    {{ number_format($hargas[$i][$j]->harga,0,',','.') }}
                                    <span>,-</span>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @endfor
                    </table>
                    {{-- END - TABLE PRODUKS --}}
                    @endfor
                </div>
            </div>
            <div class="w-56"></div>
        </div>
    </div>
</main>

<script>
    const label_supplier = {!! json_encode($label_supplier, JSON_HEX_TAG) !!}
    const label_produk = {!! json_encode($label_produk, JSON_HEX_TAG) !!}

    $("#supplier_nama").autocomplete({
        source: label_supplier,
        select: function(event, ui) {
            $("#supplier_id").val(ui.item.id);
        }
    });
    $("#produk_new-supplier_nama").autocomplete({
        source: label_supplier,
        select: function(event, ui) {
            $("#produk_new-supplier_id").val(ui.item.id);
        }
    });

    $("#produk_nama").autocomplete({
        source: label_produk,
        select: function(event, ui) {
            $("#produk_id").val(ui.item.id);
        }
    });
    $("#produk_new-produk_nama").autocomplete({
        source: label_produk,
        select: function(event, ui) {
            $("#nama_nota").val("SJ " + ui.item.value);
        }
    });

    function set_autocomplete_item(index) {
        let harga_main = document.getElementById(`harga_main-${index}`);
        let harga_t = document.getElementById(`harga_t-${index}`);
        $(`#barang_nama-${index}`).autocomplete({
            source: label_barang,
            select: function (event, ui) {
                // console.log(ui.item);
                document.getElementById(`barang_nama-${index}`).value = ui.item.value;
                document.getElementById(`barang_id-${index}`).value = ui.item.id;
                document.getElementById(`satuan_sub-${index}`).textContent = ui.item.satuan_sub;
                if (ui.item.satuan_sub !== null) {
                    document.getElementById(`jumlah_sub-${index}`).value = 1;
                }
                let satuan_mains = document.querySelectorAll(`.satuan_main-${index}`);
                for (let index = 0; index < satuan_mains.length; index++) {
                    satuan_mains[index].textContent = ui.item.satuan_main;
                }
                document.getElementById(`jumlah_main-${index}`).value = ui.item.jumlah_main/100;
                harga_main.value = ui.item.harga_main;
                document.getElementById(`harga_main_real-${index}`).value = ui.item.harga_main;
                harga_t.value = ui.item.harga_total_main;
                document.getElementById(`harga_t_real-${index}`).value = ui.item.harga_total_main;
                formatNumber(harga_main, `harga_main_real-${index}`);
                formatNumber(harga_t, `harga_t_real-${index}`);
                count_harga_total(index);
            }
        });
    }

    function count_harga_total(index) {
        let jumlah_sub = document.getElementById(`jumlah_sub-${index}`).value;
        if (jumlah_sub === '') {
            jumlah_sub = 1;
        }
        let jumlah_main = document.getElementById(`jumlah_main-${index}`).value;
        let harga_main = document.getElementById(`harga_main_real-${index}`).value;
        let harga_total_el = document.getElementById(`harga_t-${index}`);
        let harga_total = jumlah_sub * jumlah_main * harga_main;
        // console.log(harga_total);
        harga_total_el.value = harga_total;
        formatNumber(harga_total_el, `harga_t_real-${index}`);
        let harga_t_real_all = document.querySelectorAll('.harga_t_real');

        let harga_total_pembelian = 0;
        harga_t_real_all.forEach(harga_t => {
            harga_total_pembelian += parseInt(harga_t.value);
        });
        let harga_total_pembelian_el = document.getElementById('harga_total_pembelian');
        harga_total_pembelian_el.value = harga_total_pembelian;
        formatNumber(harga_total_pembelian_el, 'harga_total_pembelian_real');
    }

    // FUNGSI BARANG
    function count_harga_total_main() {
        let harga_main = document.getElementById('harga_main-real').value;
        let jumlah_main = document.getElementById('jumlah_main').value;
        let harga_total_main_el = document.getElementById('harga_total_main');

        let harga_total_main = 0;
        if (jumlah_main !== '' && harga_main !== '') {
            harga_total_main = jumlah_main * harga_main;
            harga_total_main_el.value = harga_total_main;
            formatNumber(harga_total_main_el, 'harga_total_main-real');
            let harga_sub = document.getElementById('harga_sub');
            harga_sub.value = harga_total_main;
            formatNumber(harga_sub, 'harga_sub-real');
        }
        // console.log(harga_main);
        // console.log(jumlah_main);
        // console.log(harga_total_main);
    }

    function copy_to_harga_sub() {
        let harga_total_main_real = document.getElementById('harga_total_main-real');
        // console.log(harga_total_main_real.value);
        let harga_sub = document.getElementById('harga_sub');

        harga_sub.value = harga_total_main_real.value;
        formatNumber(harga_sub, 'harga_sub-real')
    }

    function count_harga_total_sub() {
        let harga_sub = document.getElementById('harga_sub-real').value;
        let jumlah_sub = document.getElementById('jumlah_sub').value;
        let harga_total_sub_el = document.getElementById('harga_total_sub');

        let harga_total_sub = 0;
        if (jumlah_sub !== '' && harga_sub !== '') {
            harga_total_sub = jumlah_sub * harga_sub;
            harga_total_sub_el.value = harga_total_sub;
            formatNumber(harga_total_sub_el, 'harga_total_sub-real');
        }
    }
    // END - FUNGSI BARANG
    function toggle_light_class(btn_id, btn_classes, classes, id_to_show, classes_to_remove,classes_to_add) {
        // console.log(classes);
        if (id_to_show === 'all') {
            $(`.${classes}`).show();
        } else {
            $(`.${classes}`).hide();
            $(`#${id_to_show}`).show();
        }
        // console.log(classes);
        let all_btn = document.querySelectorAll(`.${btn_classes}`);
        all_btn.forEach(btn => {
            classes_to_add.forEach(class_to_add => {
                btn.classList.remove(class_to_add);
            });
        });

        let detail_button = document.getElementById(btn_id);

        classes_to_remove.forEach((element) => {
            detail_button.classList.remove(element);
        });
        classes_to_add.forEach((element) => {
            detail_button.classList.add(element);
        });
    }

    function generate_nama_nota(value) {
        document.getElementById('nama_nota').value = 'SJ ' + value;
    }
</script>

@endsection
