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
    <div class="mx-1 py-1 sm:px-6 lg:px-8 relative">
        <div class="text-center">
            <h1 class="text-xl font-bold underline">JURNAL</h1>
        </div>
        <div class="flex">
            <div id="filter-content">
                <div class="rounded p-2 bg-white shadow drop-shadow">
                    <form action="" method="GET" class="text-xs">
                        <div class="flex items-end">
                            <div>
                                <label>Kode:</label>
                                <div class="flex mt-1">
                                    <input type="text" class="border rounded text-xs p-1" name="kode" placeholder="Kode" id="filter-kode">
                                </div>
                            </div>
                            <div class="ml-2">
                                <label>Desc:</label>
                                <div class="flex mt-1">
                                    <input type="text" class="border rounded text-xs p-1" name="desc" placeholder="Deskripsi/Keterangan" id="filter-desc">
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center mt-1">
                            <div><input type="radio" name="timerange" value="triwulan" id="triwulan" onclick="set_time_range('triwulan')"><label for="triwulan" class="ml-1">triwulan</label></div>
                            <div class="ml-3"><input type="radio" name="timerange" value="7d" id="7d" onclick="set_time_range('7d')"><label for="7d" class="ml-1">7d</label></div>
                            <div class="ml-3"><input type="radio" name="timerange" value="bulan_ini" id="bulan_ini" onclick="set_time_range('bulan_ini')"><label for="bulan_ini" class="ml-1">bulan ini</label></div>
                            <div class="ml-3"><input type="radio" name="timerange" value="bulan_lalu" id="bulan_lalu" onclick="set_time_range('bulan_lalu')"><label for="bulan_lalu" class="ml-1">bulan lalu</label></div>
                            <div class="ml-3"><input type="radio" name="timerange" value="this_year" id="tahun_ini" onclick="set_time_range('tahun_ini')"><label for="tahun_ini" class="ml-1">tahun ini</label></div>
                            <div class="ml-3"><input type="radio" name="timerange" value="last_year" id="tahun_lalu" onclick="set_time_range('tahun_lalu')"><label for="tahun_lalu" class="ml-1">tahun lalu</label></div>
                        </div>
                        <div class="mt-1 flex items-end">
                            <div class="flex">
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
                                    </div>
                                </div>
                            </div>
                            <div class="text-xs">
                                <button type="submit" class="ml-2 flex items-center bg-yellow-500 text-white py-1 px-3 rounded hover:bg-yellow-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                    </svg>
                                    <span class="ml-1">filter</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- TRANSACTIONS --}}
        <div class="mt-2">
            <table class="text-xs table-border w-3/4 max-w-full">
                <tr>
                    <th></th><th></th><th></th>
                    <th>
                        <div class="flex justify-between bg-pink-300">
                            <span>Rp</span>
                            <span>{{ number_format($keluar_total,0,',','.') }}</span>
                            <span> ,-</span>
                        </div>
                    </th>
                    <th>
                        <div class="flex justify-between bg-emerald-300">
                            <span>Rp</span>
                            <span>{{ number_format($masuk_total,0,',','.') }}</span>
                            <span> ,-</span>
                        </div>
                    </th>
                    <th>
                        <div class="flex justify-between bg-yellow-300">
                            <span>Rp</span>
                            <span>{{ number_format($balance_total,0,',','.') }}</span>
                            <span> ,-</span>
                        </div>
                    </th>
                </tr>
                <tr class="bg-blue-500 text-white"><th>TANGGAL</th><th>KODE</th><th>KETERANGAN</th><th>KELUAR TOTAL</th><th>MASUK TOTAL</th><th>BALANCE TOTAL</th></tr>

                {{-- <tr>
                    <td></td>
                    <td></td>
                    <td><span class="font-bold text-orange-400">SALDO AWAL</span></td>
                    <td></td><td></td>
                    <td>
                        <div class="flex justify-between">
                            <span>Rp.</span>
                            <span>{{ number_format($saldo_awal,0,',','.') }} ,-</span>
                        </div>
                    </td>
                </tr> --}}
                @foreach ($accountings->groupBy('user_instance_id') as $key_accountings => $accountings_grouped)
                @foreach ($accountings_grouped as $key_accounting => $accounting)
                @if ($key_accounting === 0)
                <tr>
                    <td colspan="3">
                        <div class="bg-violet-200 py-1 pl-1 font-semibold text-slate-500">
                            {{ $accounting->instance_type }} - {{ $accounting->instance_name }} - {{ $accounting->username }}
                        </div>
                    </td>
                    <td>
                        <div class="flex justify-between font-semibold text-pink-500">
                            <span>Rp</span>
                            <span>{{ number_format($keluar[$key_accountings],0,',','.') }}</span>
                            <span> ,-</span>
                        </div>
                    </td>
                    <td>
                        <div class="flex justify-between font-semibold text-emerald-500">
                            <span>Rp</span>
                            <span>{{ number_format($masuk[$key_accountings],0,',','.') }}</span>
                            <span> ,-</span>
                        </div>
                    </td>
                    <td>
                        <div class="flex justify-between font-semibold text-violet-500">
                            <span>Rp</span>
                            <span>{{ number_format($balance[$key_accountings],0,',','.') }}</span>
                            <span> ,-</span>
                        </div>
                    </td>
                </tr>
                @endif
                <tr>
                    <td>{{ date('d-m-Y', strtotime($accounting->created_at)) }}</td>
                    <td>{{ $accounting->kode }}</td>
                    @if ($accounting->keterangan !== null)
                    <td>{{ $accounting->transaction_desc }} ({{ $accounting->keterangan }})</td>
                    @else
                    <td>{{ $accounting->transaction_desc }}</td>
                    @endif
                    <td>
                        @if ($accounting->transaction_type === 'pengeluaran')
                        <div class="flex justify-between">
                            <span>Rp</span>
                            <span>{{ number_format($accounting->jumlah,0,',','.') }} ,-</span>
                        </div>
                        @endif
                    </td>
                    <td>
                        @if ($accounting->transaction_type === 'pemasukan')
                        <div class="flex justify-between">
                            <span>Rp</span>
                            <span>{{ number_format($accounting->jumlah,0,',','.') }} ,-</span>
                        </div>
                        @endif
                    </td>
                    {{-- <td>
                        <div class="flex justify-between">
                            <span>Rp</span>
                            <span>{{ number_format($accounting->saldo,0,',','.') }} ,-</span>
                        </div>
                    </td> --}}
                </tr>
                @endforeach
                @endforeach
                {{-- @foreach ($accountings as $key_accounting => $accounting)
                <tr>
                    <td>{{ date('d-m-Y', strtotime($accounting->created_at)) }}</td>
                    <td>{{ $accounting->kode }}</td>
                    @if ($accounting->keterangan !== null)
                    <td>{{ $accounting->transaction_desc }} ({{ $accounting->keterangan }})</td>
                    @else
                    <td>{{ $accounting->transaction_desc }}</td>
                    @endif
                    <td>
                        @if ($accounting->transaction_type === 'pengeluaran')
                        <div class="flex justify-between">
                            <span>Rp</span>
                            <span>{{ number_format($accounting->jumlah,0,',','.') }} ,-</span>
                        </div>
                        @endif
                    </td>
                    <td>
                        @if ($accounting->transaction_type === 'pemasukan')
                        <div class="flex justify-between">
                            <span>Rp</span>
                            <span>{{ number_format($accounting->jumlah,0,',','.') }} ,-</span>
                        </div>
                        @endif
                    </td>
                    <td>
                        <div class="flex justify-between">
                            <span>Rp</span>
                            <span>{{ number_format($accounting->saldo,0,',','.') }} ,-</span>
                        </div>
                    </td>
                </tr>
                @endforeach --}}
            </table>
        </div>
        {{-- END - TRANSACTIONS --}}
    </div>
</main>

<style>
    .table-border td {
        border-bottom: 1px solid lightgrey;
        border-collapse: collapse;
        padding: 5px;
    }

</style>

<script>
    const label_deskripsi = {!! json_encode($label_deskripsi, JSON_HEX_TAG) !!};

    $(`#filter-desc`).autocomplete({
        source: label_deskripsi,
        select: function (event, ui) {
            document.getElementById(`filter-desc`).value = ui.item.value;
        }
    });

    function table_to_excel(table_id) {
        $(`#${table_id}`).table2excel({
            filename:`${table_id}.xls`
        });
    }
</script>

@endsection
