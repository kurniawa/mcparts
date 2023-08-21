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
                    <button type="submit" class="border rounded border-emerald-300 text-emerald-500 font-semibold px-3 py-1 ml-1" id="btn_new_pembelian" onclick="toggle_light(this.id, 'form_new_pembelian', [], ['bg-emerald-200'], 'block')">+ Pembelian</button>
                    <button type="submit" class="border rounded border-indigo-300 text-indigo-500 font-semibold px-3 py-1 ml-1" id="btn_new_barang" onclick="toggle_light(this.id, 'form_new_barang', [], ['bg-indigo-200'], 'block')">+ Barang</button>
                </div>
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
                                            <input type="text" name="supplier_nama" id="pembelian_new-supplier_nama" placeholder="nama supplier..." class="text-xs rounded-md border-0 py-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600">
                                            <input type="hidden" name="supplier_id" id="pembelian_new-supplier_id">
                                        </td>
                                    </tr>
                                    <tr class="align-top">
                                        <td>Ket. (opt.)</td><td><div class="mx-2">:</div></td>
                                        <td class="py-1">
                                            {{-- <input type="text" name="keterangan" placeholder="judul/keterangan..." class="text-xs rounded-md border-0 py-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600"> --}}
                                            <textarea name="keterangan" id="" cols="30" rows="5" class="border rounded p-1 text-xs" placeholder="keterangan (opt.)"></textarea>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="mt-2">
                                <table id="table_pembelian_items" class="text-slate-500 w-full">
                                    <tr><th>Nama Item</th><th>Jml. Sub</th><th>Jml. Main</th><th>Hrg.</th><th>Hrg. t</th><th></th></tr>
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
                                                    <input type="text" id="barang_new-harga_main" class="text-xs rounded p-1" oninput="formatNumber(this, 'barang_new-harga_main-real');count_harga_total_main()">
                                                    <input type="hidden" name="harga_main" id="barang_new-harga_main-real">
                                                </td>
                                                <td>Harga Total</td><td><div class="mx-1">:</div></td>
                                                <td>
                                                    <input type="text" name="harga_total_main" id="barang_new-harga_total_main" class="text-xs rounded p-1" oninput="formatNumber(this, 'barang_new-harga_total_main-real');copy_to_harga_sub();count_harga_total_sub()">
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
                                                    <input type="text" id="barang_new-harga_sub" class="text-xs rounded p-1" oninput="formatNumber(this, 'barang_new-harga_sub-real');count_harga_total_sub()">
                                                    <input type="hidden" name="harga_sub" id="barang_new-harga_sub-real">
                                                </td>
                                                <td>Harga Total</td><td><div class="mx-1">:</div></td>
                                                <td>
                                                    <input type="text" name="harga_total_sub" id="barang_new-harga_total_sub" class="text-xs rounded p-1" oninput="formatNumber(this, 'barang_new-harga_total_sub-real');">
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
                            <button type="submit" class="border-2 border-indigo-300 bg-indigo-200 text-indigo-600 rounded-lg font-semibold py-1 px-3 hover:bg-indigo-300">+ Barang</button>
                        </div>
                    </form>
                </div>
            </div>
            {{-- END - FORM_NEW_BARANG --}}
            <div class="flex justify-center">
                <div class='pb-1 text-xs lg:w-1/2 md:w-3/4'>
                    <table class="table-nice w-full">
                        <tr>
                            <th>Grand Total</th>
                            <th>
                                <div class="flex justify-between bg-pink-200">
                                    <span>Rp</span>
                                    <span>{{ number_format(($grand_total - $lunas_total), 0,',','.') }}</span>
                                    <span> ,-</span>
                                </div>
                            </th>
                            <th>
                                <div class="flex justify-between bg-emerald-200">
                                    <span>Rp</span>
                                    <span>{{ number_format($lunas_total, 0,',','.') }}</span>
                                    <span> ,-</span>
                                </div>
                            </th>
                            <th>
                                <div class="flex justify-between">
                                    <span>Rp</span>
                                    <span>{{ number_format($grand_total, 0,',','.') }}</span>
                                    <span> ,-</span>
                                </div>
                            </th>
                        </tr>
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
                            {{-- <td>
                                <div>{{ $pembelian_barangs_all[$i][0]->barang_nama }}</div>
                                @if (count($pembelian_barangs_all[$i]) > 1)
                                <div class="text-blue-500">+{{ count($pembelian_barangs_all[$i]) - 1 }} barang lainnya</div>
                                @endif
                            </td> --}}
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
                            <td colspan="4">
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

                                {{-- PEMBELIAN_ITEMS --}}
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
                                        <td colspan="4">
                                            <div>
                                                Content:
                                                @if ($pembelians[$i]->isi !== null)
                                                    @foreach (json_decode($pembelians[$i]->isi,true) as $isi)
                                                        --> {{ $isi['jumlah'] / 100 }} {{ $isi['satuan'] }}
                                                    @endforeach
                                                @endif
                                            </div>
                                        </td>
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
                                {{-- END - PEMBELIAN_ITEMS --}}
                            </td>
                            <td class="align-bottom">
                                <div>
                                    <a href="{{ route('pembelians.edit', $pembelians[$i]->id) }}">
                                        <button class="rounded bg-slate-200 text-slate-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                        </button>
                                    </a>
                                </div>
                                <form action="{{ route('pembelians.delete', $pembelians[$i]->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pembelian ini?')">
                                    @csrf
                                    <button type="submit" class="text-red-500 bg-red-200 rounded">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </form>
                                <div>
                                    <button id="btn_form_pelunasan" class="text-emerald-500 border border-emerald-300 rounded" onclick="toggle_light(this.id, 'form_pelunasan-{{ $i }}', [], ['bg-emerald-200'], 'table-row')">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        {{-- FORM PELUNASAN --}}
                        <tr class="hidden" id="form_pelunasan-{{ $i }}">
                            <td colspan="7">
                                <form action="{{ route('pembelians.pelunasan', $pembelians[$i]->id) }}" method="POST" class="flex justify-end">
                                    @csrf
                                    <table>
                                        <tr>
                                            <td>Tanggal Lunas</td><td>:</td>
                                            <td>
                                                <div class="flex">
                                                    @if ($pembelians[$i]->status_bayar === 'LUNAS')
                                                    <input type="text" name="day" id="day" class="border rounded text-xs p-1 w-8" placeholder="dd" value="{{ date('d', strtotime($pembelians[$i]->tanggal_lunas)) }}">
                                                    <input type="text" name="month" id="month" class="border rounded text-xs p-1 w-8 ml-1" placeholder="mm" value="{{ date('m'), strtotime($pembelians[$i]->tanggal_lunas) }}">
                                                    <input type="text" name="year" id="year" class="border rounded text-xs p-1 w-11 ml-1" placeholder="yyyy" value="{{ date('Y'), strtotime($pembelians[$i]->tanggal_lunas) }}">
                                                    @else
                                                    <input type="text" name="day" id="day" class="border rounded text-xs p-1 w-8" placeholder="dd" value="{{ date('d') }}">
                                                    <input type="text" name="month" id="month" class="border rounded text-xs p-1 w-8 ml-1" placeholder="mm" value="{{ date('m') }}">
                                                    <input type="text" name="year" id="year" class="border rounded text-xs p-1 w-11 ml-1" placeholder="yyyy" value="{{ date('Y') }}">
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <div class="text-end">
                                                    <button class="border-2 border-emerald-300 bg-emerald-100 rounded text-emerald-500 px-1">Confirm</button>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </form>
                            </td>
                        </tr>
                        {{-- FORM PELUNASAN --}}
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

    $("#pembelian_new-supplier_nama").autocomplete({
        source: label_supplier,
        select: function(event, ui) {
            $("#pembelian_new-supplier_id").val(ui.item.id);
            $("#pembelian_new-supplier_nama").val(ui.item.value);
        }
    });

    $("#barang_new-supplier_nama").autocomplete({
        source: label_supplier,
        select: function(event, ui) {
            $("#barang_new-supplier_id").val(ui.item.id);
        }
    });

    let index_item = 0;
    function add_item(tr_id, parent_id) {
        document.getElementById(tr_id).remove();
        let parent = document.getElementById(parent_id);
        parent.insertAdjacentHTML('beforeend',
        `<tr id="tr_barang-${index_item}">
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
            <td>
                <button type="button" class="text-red-500" onclick="remove_item(${index_item})">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </button>
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

    $("#barang_new-barang_nama").autocomplete({
        source: label_barang,
        select: function(event, ui) {
            $("#barang_new-barang_id").val(ui.item.id);
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
        // console.log(harga_main);
        // console.log(jumlah_main);
        // console.log(harga_total_main);
    }

    function copy_to_harga_sub() {
        let harga_total_main_real = document.getElementById('barang_new-harga_total_main-real');
        // console.log(harga_total_main_real.value);
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

    function remove_item(index) {
        // console.log(index);
        document.getElementById(`tr_barang-${index}`).remove();
    }
</script>

@endsection
