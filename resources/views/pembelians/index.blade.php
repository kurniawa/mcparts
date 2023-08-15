@extends('layouts.main')
@section('content')
{{-- <header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <h1 class="text-3xl font-bold tracking-tight text-gray-900">App</h1>
    </div>
  </header> --}}
<main>
    <x-errors-any></x-errors-any>
    <x-validation-feedback></x-validation-feedback>
    {{-- SEARCH / FILTER --}}
    <div class="mx-1 py-1 sm:px-6 lg:px-8 text-xs">
        <div class="flex">
            <button id="filter" class="border rounded border-yellow-500 text-yellow-500 px-3 py-1" onclick="toggle_light(this.id,'filter-content',[],['bg-yellow-200'], 'block')">Filter</button>
            <button type="submit" class="border rounded border-emerald-300 text-emerald-500 font-semibold px-3 py-1 ml-1" id="btn_new_pembelian" onclick="toggle_light(this.id, 'form_new_pembelian', [], ['bg-emerald-200'], 'block')">+ Pembelian</button>
        </div>
        {{-- FORM_NEW_PEMBELIAN --}}
        <div id="form_new_pembelian" class="hidden">
            <div class="flex justify-center">
                <form action="{{ route('pembelians.store') }}" method="POST" class="border rounded border-emerald-300 p-1 mt-1 lg:w-3/5 md:w-3/4">
                    @csrf
                    <div class="border rounded p-2">
                        <div class="border-b pb-3">
                            <table>
                                <tr>
                                    <td>Nomor</td><td><div class="mx-2">:</div></td><td><input type="text" name="nomor_nota" class="rounded p-1 text-xs" placeholder="nomor nota ..."></td>
                                </tr>
                                <tr>
                                    <td>Tanggal</td><td><div class="mx-2">:</div></td>
                                    <td class="py-1">
                                        {{-- <div class="flex">
                                            <select name="day" id="day" class="rounded text-xs">
                                                <option value="{{ date('d') }}">{{ date('d') }}</option>
                                                @for ($i = 1; $i < 32; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                            <select name="month" id="month" class="rounded text-xs ml-1">
                                                <option value="{{ date('m') }}">{{ date('m') }}</option>
                                                @for ($i = 1; $i < 13; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                            <select name="year" id="year" class="rounded text-xs ml-1">
                                                <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                                                <option value="">-</option>
                                                @for ($i = ((int)date("Y") - 30); $i < ((int)date("Y") + 30); $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div> --}}
                                        <div class="flex">
                                            <input type="text" name="day" id="day" class="border rounded text-xs p-1 w-8" placeholder="dd" value="{{ date('d') }}">
                                            <input type="text" name="month" id="month" class="border rounded text-xs p-1 w-8 ml-1" placeholder="mm" value="{{ date('m') }}">
                                            <input type="text" name="year" id="year" class="border rounded text-xs p-1 w-11 ml-1" placeholder="yyyy" value="{{ date('Y') }}">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Supplier</td><td><div class="mx-2">:</div></td>
                                    <td class="py-1">
                                        <input type="text" name="supplier_nama" id="supplier_nama" placeholder="nama supplier..." class="text-xs rounded-md border-0 py-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600">
                                        <input type="hidden" name="supplier_id" id="supplier_id">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Ket. (opt.)</td><td><div class="mx-2">:</div></td>
                                    <td class="py-1"><input type="text" name="keterangan" placeholder="judul/keterangan..." class="text-xs rounded-md border-0 py-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600"></td>
                                </tr>
                            </table>
                        </div>
                        <div class="mt-2">
                            <table id="table_pembelian_items" class="text-slate-500 w-full">
                                <tr><th>Nama Item</th><th>Jml. Sub</th><th>Jml. Main</th><th>Hrg.</th><th>Hrg. t</th></tr>
                                <tr id="tr_add_item">
                                    <td>
                                        <button type="button" class="rounded bg-emerald-200 text-emerald-600" onclick="add_item('tr_add_item','table_pembelian_items')">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </table>
                            <div class="flex justify-end items-center">
                                <span class="font-bold">Total</span>
                                <div class="flex font-bold ml-2 items-center text-pink-500">
                                    <span>Rp</span>
                                    <input type="text" id="harga_total_pembelian" class="border-none p-1 w-28 ml-2" readonly>
                                    <span class="ml-1">,-</span>
                                </div>
                                <input type="hidden" name="harga_total" id="harga_total_pembelian_real">
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-center mt-3">
                        <button type="submit" class="border-2 border-emerald-300 bg-emerald-200 text-emerald-600 rounded-lg font-semibold py-1 px-3 hover:bg-emerald-300">Proses/Konfirmasi</button>
                    </div>
                </form>
            </div>
        </div>
        {{-- END - FORM_NEW_PEMBELIAN --}}

        <div class="hidden" id="filter-content">
            <div class="rounded p-2 bg-white shadow drop-shadow inline-block mt-1">
                <form action="" method="GET">
                    <div class="ml-1 mt-2 flex">
                        <div>
                            <label>Supplier:</label>
                            <div class="flex mt-1">
                                <input type="text" class="border rounded text-xs p-1" name="supplier_nama" placeholder="Nama Supplier..." id="supplier_nama">
                                <input type="hidden" name="supplier_id" id="supplier_id">
                            </div>
                        </div>
                        <div class="flex items-center ml-2">
                            <div><input type="radio" name="timerange" value="today" id="now" onclick="set_time_range('now')"><label for="now" class="ml-1">now</label></div>
                            <div class="ml-3"><input type="radio" name="timerange" value="7d" id="7d" onclick="set_time_range('7d')"><label for="7d" class="ml-1">7d</label></div>
                            {{-- <div class="ml-3"><input type="radio" name="timerange" value="30d" id="30d" onclick="set_time_range('30d')"><label for="30d" class="ml-1">30d</label></div> --}}
                            <div class="ml-3"><input type="radio" name="timerange" value="bulan_ini" id="bulan_ini" onclick="set_time_range('bulan_ini')"><label for="bulan_ini" class="ml-1">bulan ini</label></div>
                            <div class="ml-3"><input type="radio" name="timerange" value="bulan_lalu" id="bulan_lalu" onclick="set_time_range('bulan_lalu')"><label for="bulan_lalu" class="ml-1">bulan lalu</label></div>
                            <div class="ml-3"><input type="radio" name="timerange" value="this_year" id="tahun_ini" onclick="set_time_range('tahun_ini')"><label for="tahun_ini" class="ml-1">tahun ini</label></div>
                            <div class="ml-3"><input type="radio" name="timerange" value="last_year" id="tahun_lalu" onclick="set_time_range('tahun_lalu')"><label for="tahun_lalu" class="ml-1">tahun lalu</label></div>
                        </div>
                    </div>
                    <div class="flex mt-2">
                        <div class="ml-1 flex">
                            <div>
                                <label>Dari:</label>
                                <div class="flex">
                                    <select name="from_day" id="from_day" class="rounded text-xs py-1">
                                        <option value="">-</option>
                                        @for ($i = 1; $i < 32; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                    <select name="from_month" id="from_month" class="rounded text-xs py-1 ml-1">
                                        <option value="">-</option>
                                        @for ($i = 1; $i < 13; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                    <select name="from_year" id="from_year" class="rounded text-xs py-1 ml-1">
                                        <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                                        <option value="">-</option>
                                        @for ($i = ((int)date("Y") - 30); $i < ((int)date("Y") + 30); $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="ml-3">
                                <label>Sampai:</label>
                                <div class="flex items-center">
                                    <select name="to_day" id="to_day" class="rounded text-xs py-1">
                                        <option value="">-</option>
                                        @for ($i = 1; $i < 32; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                    <select name="to_month" id="to_month" class="rounded text-xs py-1 ml-1">
                                        <option value="">-</option>
                                        @for ($i = 1; $i < 13; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                    <select name="to_year" id="to_year" class="rounded text-xs py-1 ml-1">
                                        <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                                        <option value="">-</option>
                                        @for ($i = ((int)date("Y") - 30); $i < ((int)date("Y") + 30); $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                    <button type="submit" class="ml-2 flex items-center bg-orange-500 text-white py-1 px-3 rounded hover:bg-orange-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                        </svg>
                                        <span class="ml-1">Search</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- END - SEARCH / FILTER --}}
    <div class="flex justify-center">
        <div class='pb-1 text-xs lg:w-1/2 md:w-3/4'>
            <table class="table-nice w-full">
                @for ($i = 0; $i < count($pembelians); $i++)
                <tr class="border-b">
                    <td>
                        <div class="flex">
                            <div class="flex">
                                @if ($pembelians[$i]->tanggal_lunas === null)
                                <div>
                                    <div class="rounded p-1 bg-pink-200 text-pink-500 font-bold text-center">
                                        <div class="min-w-max">{{ date('d',strtotime($pembelians[$i]->created_at)) }}</div>
                                        <div class="min-w-max">{{ date('m-y',strtotime($pembelians[$i]->created_at)) }}</div>
                                    </div>
                                </div>
                                @else
                                <div>
                                    <div class="rounded p-1 bg-sky-200 text-sky-500 font-bold text-center">
                                        <div class="min-w-max">{{ date('d',strtotime($pembelians[$i]->created_at)) }}</div>
                                        <div class="min-w-max">{{ date('m-y',strtotime($pembelians[$i]->created_at)) }}</div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="flex ml-1 items-center">
                                @if ($pembelians[$i]->tanggal_lunas !== null)
                                <div>
                                    <div class="rounded p-1 bg-emerald-200 text-emerald-500 font-bold text-center">
                                        <div class="min-w-max">{{ date('d',strtotime($pembelians[$i]->tanggal_lunas)) }}</div>
                                        <div class="min-w-max">{{ date('m-y',strtotime($pembelians[$i]->tanggal_lunas)) }}</div>
                                    </div>
                                </div>
                                @else
                                <span class="font-bold">--</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <a href="{{ route('pembelians.show', $pembelians[$i]->id) }}" class="text-sky-500">
                            <div class="min-w-max">
                                @if ($alamats[$i] !== null)
                                {{ $pembelians[$i]['supplier_nama'] }} - {{ $alamats[$i]['short'] }}
                                @else
                                {{ $pembelians[$i]['supplier_nama'] }}
                                @endif
                            </div>
                        </a>
                    </td>
                    <td><a href="{{ route('pembelians.show', $pembelians[$i]->id) }}" class="text-indigo-500">{{ $pembelians[$i]->nomor_nota }}</a></td>
                    <td>
                        <div>{{ $pembelian_barangs_all[$i][0]->barang_nama }}</div>
                        @if (count($pembelian_barangs_all[$i]) > 1)
                        <div class="text-blue-500">+{{ count($pembelian_barangs_all[$i]) - 1 }} barang lainnya</div>
                        @endif
                    </td>
                    <td>
                        <div class="flex justify-between font-semibold">
                            <span>Rp</span>
                            {{ number_format($pembelians[$i]->harga_total,0,',','.') }}
                            <span>,-</span>
                        </div>
                    </td>
                    <td>
                        <button id="btn_detail_pembelian-{{ $i }}" class="border rounded" onclick="showDropdown(this.id, 'detail_pembelian-{{ $i }}')">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>
                    </td>
                </tr>
                <tr class="hidden" id="detail_pembelian-{{ $i }}">
                    <td colspan="5">
                        {{-- <table>
                            <tr>
                                <td>
                                    @if ($alamats[$i]!==null)
                                    @if ($alamats[$i]['long']!==null)
                                    @foreach (json_decode($alamats[$i]['long'],true) as $alamat)
                                    {{ $alamat }}<br>
                                    @endforeach
                                    @endif
                                    @else
                                    -
                                    @endif
                                </td>
                                <td>
                                    @if ($kontaks[$i]!==null)
                                    @if ($kontaks[$i]['kodearea']!==null)
                                    {{ $kontaks[$i]['kodearea'] }} {{ $kontaks[$i]['nomor'] }}
                                    @else
                                    {{ $kontaks[$i]['nomor'] }}
                                    @endif
                                    @else
                                    -
                                    @endif
                                </td>
                            </tr>
                        </table> --}}
                        <table class="w-full">
                            @foreach ($pembelian_barangs_all[$i] as $key_pembelian_barang => $pembelian_barang)
                            <tr>
                                <td>{{ $key_pembelian_barang + 1 }}.</td>
                                <td><div class="min-w-max">{{ $pembelian_barang->barang_nama }}</div></td>
                                <td>
                                    @if ($pembelian_barang->satuan_sub !== null)
                                    <div class="min-w-max">
                                        {{ $pembelian_barang->jumlah_sub / 100 }} {{ $pembelian_barang->satuan_sub }}
                                    </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="min-w-max">
                                        {{ $pembelian_barang->jumlah_main / 100 }} {{ $pembelian_barang->satuan_main }}
                                    </div>
                                </td>
                                <td>
                                    <div class="min-w-max">
                                        Rp {{ number_format($pembelian_barang->harga_main,0,',','.') }},-/{{ $pembelian_barang->satuan_main }}
                                    </div>
                                </td>
                                <td>
                                    <div class="flex justify-between">
                                        <span>Rp</span>
                                        {{ number_format($pembelian_barang->harga_t,0,',','.') }}
                                        <span>,-</span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            <tr>
                                <td></td><td></td><td></td><td></td>
                                <th>Total</th>
                                <td>
                                    <div class="flex justify-between font-semibold">
                                        <span>Rp</span>
                                        {{ number_format($pembelians[$i]->harga_total,0,',','.') }}
                                        <span>,-</span>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                @endfor
            </table>
        </div>
    </div>
    <div class="w-56"></div>
</main>

<script>
    const label_supplier = {!! json_encode($label_supplier, JSON_HEX_TAG) !!}

    $("#supplier_nama").autocomplete({
        source: label_supplier,
        select: function(event, ui) {
            $("#supplier_id").val(ui.item.id);
        }
    });

    let index_item = 0;
    function add_item(tr_id, parent_id) {
        document.getElementById(tr_id).remove();
        let parent = document.getElementById(parent_id);
        parent.insertAdjacentHTML('beforeend',
        `<tr>
            <td>
                <div class="flex items-center mt-1">
                    <button id="toggle_barang_keterangan-${index_item}" type="button" class="border border-yellow-500 rounded text-yellow-500" onclick="toggle_light(this.id,'barang_keterangan-${index_item}', [], ['bg-yellow-300'], 'block')">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-3 h-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </button>
                    <input type="text" name="barang_nama[]" id="barang_nama-${index_item}" class="border-slate-300 rounded-lg text-xs p-1 ml-1 placeholder:text-slate-400 w-56" placeholder="nama item...">
                    <input type="hidden" name="barang_id[]" id="barang_id-${index_item}">
                </div>
                <div class="mt-1 hidden" id="barang_keterangan-${index_item}">
                    <textarea name="barang_keterangan[]" cols="30" rows="3" class="border-slate-300 rounded-lg text-xs p-0 placeholder:text-slate-400" placeholder="keterangan item..."></textarea>
                </div>
            </td>
            <td>
                <div class="text-center">
                    <div class="flex items-center">
                        <input type="text" name="jumlah_sub[]" id="jumlah_sub-${index_item}" min="1" step="1" class="border-slate-300 rounded-lg text-xs p-1 w-1/2" oninput="count_harga_total(${index_item})">
                        <span id="satuan_sub-${index_item}" class="ml-1"></span>
                    </div>
                </div>
            </td>
            <td>
                <div class="text-center">
                    <div class="flex items-center">
                        <input type="text" name="jumlah_main[]" id="jumlah_main-${index_item}" min="1" step="1" class="border-slate-300 rounded-lg text-xs p-1 w-1/2" oninput="count_harga_total(${index_item})">
                        <span class="satuan_main-${index_item} ml-1"></span>
                    </div>
                </div>
            </td>
            <td>
                <div class="text-center">
                    <div class="flex items-center">
                        <input type="text" id="harga_main-${index_item}" min="1" step="1" class="border-slate-300 rounded-lg text-xs p-1 w-3/4" oninput="formatNumber(this, 'harga_main_real-${index_item}'); count_harga_total(${index_item})">/<span class="satuan_main-${index_item} ml-1"></span>
                        <input type="hidden" name="harga_main[]" id="harga_main_real-${index_item}">
                    </div>
                </div>
            </td>
            <td>
                <div class="text-center">
                    <div class="flex">
                        <input type="text" id="harga_t-${index_item}" min="1" step="1" class="border-slate-300 rounded-lg text-xs p-1 w-full" oninput="formatNumber(this, 'harga_t_real-${index_item}');">
                        <input type="hidden" name="harga_t[]" id="harga_t_real-${index_item}" class="harga_t_real">
                    </div>
                </div>
            </td>
        </tr>
        <tr id="tr_add_item">
            <td>
                <button type="button" class="rounded bg-emerald-200 text-emerald-600" onclick="add_item('tr_add_item', 'table_pembelian_items')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </button>
            </td>
        </tr>
        `);
        setTimeout(() => {
            // set_autocomplete_item(`barang_nama-${index_item}`, `barang_nama-${index_item}`, `barang_id-${index_item}`, `satuan_sub-${index_item}`, `satuan_main-${index_item}`, `jumlah_sub-${index_item}`, `jumlah_main-${index_item}`, `harga_main-${index_item}`, `harga_t-${index_item}`);
            set_autocomplete_item(index_item);
            index_item++;
        }, 100);
    }

    const label_barang = {!! json_encode($label_barang, JSON_HEX_TAG) !!};
    // console.log(label_barang);
    // function set_autocomplete_item(input_id, label_id, value_id, satuan_sub_id, satuan_main_class, jumlah_sub_id, jumlah_main_id, harga_main_id, harga_t_id) {
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
                document.getElementById(`jumlah_main-${index}`).value = ui.item.jumlah_standar/100;
                harga_main.value = ui.item.harga_main;
                document.getElementById(`harga_main_real-${index}`).value = ui.item.harga_main;
                harga_t.value = ui.item.harga_standar;
                document.getElementById(`harga_t_real-${index}`).value = ui.item.harga_standar;
                formatNumber(harga_main, `harga_main_real-${index}`);
                formatNumber(harga_t, `harga_t_real-${index}`);
                count_harga_total(index);
            }
        });
    }

    function count_harga_total(index) {
        let jumlah_sub = document.getElementById(`jumlah_sub-${index}`).value;
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
</script>

@endsection
