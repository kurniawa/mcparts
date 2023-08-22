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
    <div class="mx-1 py-1 sm:px-6 lg:px-8 text-xs">
        <div class="flex">
            <button id="btn_filter" class="border rounded border-yellow-300 text-yellow-500 px-3 py-1" onclick="toggle_light(this.id,'filter-content',[],['bg-yellow-200'], 'block')">Filter</button>
        </div>
        {{-- SEARCH / FILTER --}}
        <div class="mt-1 hidden" id="filter-content">
            <form action="" method="GET" class="rounded p-2 bg-white shadow drop-shadow inline-block">
                <div class="ml-1 mt-2 flex">
                    <div>
                        <label>Customer:</label>
                        <div class="flex mt-1">
                            <input type="text" class="border rounded text-xs p-1" name="nama_pelanggan" placeholder="Nama Customer..." id="nama_pelanggan">
                            <input type="hidden" name="pelanggan_id" id="pelanggan_id">
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
        {{-- END - SEARCH / FILTER --}}
        <div class="flex mt-2 justify-center">
            {{-- TOTAL_PENJUALAN_PELANGGAN --}}
            <div>
                <div class="flex items-center">
                    <div class="flex items-center bg-white rounded shadow drop-shadow p-1">
                        <h3 class="font-semibold ml-2">Total Penjualan Pelanggan</h3>
                    </div>
                </div>
                <table class="table-nice mt-1">
                    <tr><th>No.</th><th>Customer</th><th>Total Penjualan</th></tr>
                    @foreach ($total_penjualan_pelanggan_all as $key_total_penjualan => $total_penjualan_pelanggan)
                    <tr>
                        <td>{{ $key_total_penjualan + 1 }}.</td><td>{{ $total_penjualan_pelanggan['pelanggan_nama'] }}</td>
                        <td>
                            <div class="flex justify-between">
                                <span>Rp</span>
                                {{ number_format($total_penjualan_pelanggan['total_penjualan'],0,',','.') }},-
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    <tr>
                        <td></td><th>Grand Total</th>
                        <td>
                            <div class="flex justify-between font-bold">
                                <span>Rp</span>
                                {{ number_format($grand_total,0,',','.') }},-
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            {{-- END - TOTAL_PENJUALAN_PELANGGAN --}}
            <div class="ml-2">
                {{-- NOTA_SUBTOTAL --}}
                <div>
                    <div class="flex items-center">
                        <div class="flex items-center bg-white rounded shadow drop-shadow p-1">
                            <h3 class="font-semibold ml-2">Nota + Subtotal</h3>
                        </div>
                        <button class="rounded bg-emerald-200 text-emerald-500 p-1 ml-1" onclick="table_to_excel('nota_subtotal_download')">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 13.5l3 3m0 0l3-3m-3 3v-6m1.06-4.19l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                            </svg>
                        </button>
                    </div>
                    <table class="table-nice mt-1">
                        <tr><th>No.</th><th>Tanggal</th><th>Pelanggan</th><th>Harga</th><th>Subtotal</th></tr>
                        @foreach ($nota_subtotal_all as $nota_subtotal)
                        <tr class="{{ $nota_subtotal['class'] }}">
                            <td>{{ $nota_subtotal['no_nota'] }}</td><td>{{ date('d-m-Y', strtotime($nota_subtotal['created_at'])) }}</td>
                            <td>{{ $nota_subtotal['pelanggan_nama'] }}</td>
                            <td>
                                <div class="flex justify-between">
                                    <span>Rp</span>
                                    {{ number_format($nota_subtotal['harga_total'],0,',','.') }},-
                                </div>
                            </td>
                            @if ($nota_subtotal['subtotal'])
                            <td class="font-semibold">
                                <div class="flex justify-between">
                                    <span>Rp</span>
                                    {{ number_format($nota_subtotal['subtotal'],0,',','.') }},-
                                </div>
                            </td>
                            @else
                            <td></td>
                            @endif
                        </tr>
                        @endforeach
                    </table>
                </div>
                {{-- END - NOTA_SUBTOTAL --}}
                {{-- NOTA_DETAIL_ITEMS --}}
                <div class="mt-2">
                    <div class="flex items-center">
                        <div class="flex items-center bg-white rounded shadow drop-shadow p-1">
                            <h3 class="font-semibold ml-2">Nota + Detail Items</h3>
                        </div>
                        <button class="rounded bg-emerald-200 text-emerald-500 p-1 ml-1" onclick="table_to_excel('nota_detail_items_download')">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 13.5l3 3m0 0l3-3m-3 3v-6m1.06-4.19l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                            </svg>
                        </button>
                    </div>
                    <table class="table-nice mt-1">
                        <tr><th>Tanggal</th><th>Ref.</th><th>Customer</th><th>Daerah</th><th>Nota Item</th><th>Jml.</th><th>Harga</th><th>Total</th></tr>
                        @foreach ($nota_detail_items_all as $nota_detail_item)
                        <tr class="{{ $nota_detail_item['class'] }}">
                            <td>{{ date('d-m-Y', strtotime($nota_detail_item['created_at'])) }}</td><td>{{ $nota_detail_item['no_nota'] }}</td>
                            <td>{{ $nota_detail_item['pelanggan_nama'] }}</td><td>{{ $nota_detail_item['cust_short'] }}</td>
                            <td>{{ $nota_detail_item['nama_nota'] }}</td><td>{{ $nota_detail_item['jumlah'] }}</td>
                            <td>
                                <div class="flex justify-between">
                                    <span>Rp</span>
                                    {{ number_format($nota_detail_item['harga'],0,',','.') }},-
                                </div>
                            </td>
                            <td>
                                <div class="flex justify-between">
                                    <span>Rp</span>
                                    {{ number_format($nota_detail_item['harga_t'],0,',','.') }},-
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </table>
                </div>
                {{-- END - NOTA_DETAIL_ITEMS --}}
            </div>
        </div>
    </div>

    {{-- VERSI UNTUK DOWNLOAD --}}
    <table id="nota_subtotal_download" class="hidden">
        <tr><th>No.</th><th>Tanggal</th><th>Pelanggan</th><th>Harga</th><th>Subtotal</th></tr>
        @foreach ($nota_subtotal_all as $nota_subtotal)
        <tr>
            <td>{{ $nota_subtotal['no_nota'] }}</td><td>{{ date('d-m-Y', strtotime($nota_subtotal['created_at'])) }}</td>
            <td>{{ $nota_subtotal['pelanggan_nama'] }}</td>
            <td>{{ $nota_subtotal['harga_total'] }}</td>
            @if ($nota_subtotal['subtotal'])
            <td class="font-semibold">{{ $nota_subtotal['subtotal'] }}</td>
            @else
            <td></td>
            @endif
        </tr>
        @endforeach
    </table>

    <table id="nota_detail_items_download" class="hidden">
        <tr><th>Tanggal</th><th>Ref.</th><th>Customer</th><th>Daerah</th><th>Nota Item</th><th>Jml.</th><th>Harga</th><th>Total</th></tr>
        @foreach ($nota_detail_items_all as $nota_detail_item)
        <tr>
            <td>{{ date('d-m-Y', strtotime($nota_detail_item['created_at'])) }}</td><td>{{ $nota_detail_item['no_nota'] }}</td>
            <td>{{ $nota_detail_item['pelanggan_nama'] }}</td><td>{{ $nota_detail_item['cust_short'] }}</td>
            <td>{{ $nota_detail_item['nama_nota'] }}</td><td>{{ $nota_detail_item['jumlah'] }}</td>
            <td>{{ $nota_detail_item['harga'] }}</td>
            <td>{{ $nota_detail_item['harga_t'] }}</td>
        </tr>
        @endforeach
    </table>
    {{-- END - VERSI UNTUK DOWNLOAD --}}
    <div class="bg-orange-50"></div>
    <div class="bg-sky-100"></div>
</main>

<script>
    function table_to_excel(table_id) {
        $(`#${table_id}`).table2excel({
            filename:`${table_id}.xls`
        });
    }
</script>

@endsection
