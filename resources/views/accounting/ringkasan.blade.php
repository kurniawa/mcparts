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
        <div class="flex">
            <div>
                <h1 class="text-xl font-bold">RINGKASAN ARUS KAS</h1>
                <h2 class="text-xl font-bold">MC-PARTS .CV</h2>
                <h2 class="text-xl font-bold">{{ date('d M Y', strtotime($from)) }} - {{ date('d M Y', strtotime($until)) }}</h2>
            </div>
            <div class="flex ml-3">
                <div id="filter-content">
                    <div class="rounded p-2 bg-white shadow drop-shadow">
                        <form action="" method="GET" class="text-xs">
                            <div class="flex items-end">
                                <div class="flex items-center ml-2">
                                    <div><input type="radio" name="timerange" value="triwulan" id="triwulan" onclick="set_time_range('triwulan')"><label for="triwulan" class="ml-1">triwulan</label></div>
                                    <div class="ml-3"><input type="radio" name="timerange" value="7d" id="7d" onclick="set_time_range('7d')"><label for="7d" class="ml-1">7d</label></div>
                                    <div class="ml-3"><input type="radio" name="timerange" value="bulan_ini" id="bulan_ini" onclick="set_time_range('bulan_ini')"><label for="bulan_ini" class="ml-1">bulan ini</label></div>
                                    <div class="ml-3"><input type="radio" name="timerange" value="bulan_lalu" id="bulan_lalu" onclick="set_time_range('bulan_lalu')"><label for="bulan_lalu" class="ml-1">bulan lalu</label></div>
                                    <div class="ml-3"><input type="radio" name="timerange" value="this_year" id="tahun_ini" onclick="set_time_range('tahun_ini')"><label for="tahun_ini" class="ml-1">tahun ini</label></div>
                                    <div class="ml-3"><input type="radio" name="timerange" value="last_year" id="tahun_lalu" onclick="set_time_range('tahun_lalu')"><label for="tahun_lalu" class="ml-1">tahun lalu</label></div>
                                </div>
                            </div>
                            <div class="mt-2 flex items-end">
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
        </div>

        {{-- RINGKASANS --}}
        <div class="mt-2">
            <table class="text-xs table-border w-3/4 max-w-full">
                <tr>
                    <th></th>
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
                            <span>{{ number_format(($masuk_total - $keluar_total),0,',','.') }}</span>
                            <span> ,-</span>
                        </div>
                    </th>
                </tr>
                <tr>
                    <th rowspan="2" class="bg-blue-500 text-white">KODE AKUN</th>
                    <th colspan="2" class="bg-blue-500 text-white">NERACA SALDO</th>
                    <td rowspan="2">
                        <div class="flex justify-center">
                            <button class="rounded bg-emerald-400 text-white p-1" onclick="table_to_excel('table-ringkasan')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 13.5l3 3m0 0l3-3m-3 3v-6m1.06-4.19l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>KELUAR</th><th>MASUK</th>
                </tr>


                @foreach ($ringkasans as $key_ringkasan => $ringkasan)
                @if ($key_ringkasan !== 0)
                <tr><th></th><td></td><td></td></tr>
                @endif
                <tr>
                    @if ($ringkasan['type'] === 'UANG MASUK')
                    <th class="bg-emerald-300">{{ $ringkasan['type'] }}</th>
                    @elseif ($ringkasan['type'] === 'UANG KELUAR')
                    <th class="bg-yellow-300">{{ $ringkasan['type'] }}</th>
                    @endif
                </tr>

                @foreach ($ringkasan['kategori_level_one'] as $kategori_level_one)
                @if (isset($kategori_level_one['kategori_level_two']))
                <tr><td></td><td></td><td></td></tr>
                <tr><td colspan="3"><div class="font-bold">{{ $kategori_level_one['name'] }}</div></td></tr>
                @foreach ($kategori_level_one['kategori_level_two'] as $kategori_level_two)
                <tr>
                    <td>{{ $kategori_level_two['name'] }}</td>
                    @if ($ringkasan['type'] === 'UANG MASUK')
                    <td></td>
                    <td>
                        <div class="flex justify-between">
                            <span>Rp</span>
                            <span>{{ number_format($kategori_level_two['jumlah'],0,',','.') }}</span>
                            <span> ,-</span>
                        </div>
                    </td>
                    @elseif ($ringkasan['type'] === 'UANG KELUAR')
                    <td>
                        <div class="flex justify-between">
                            <span>Rp</span>
                            <span>{{ number_format($kategori_level_two['jumlah'],0,',','.') }}</span>
                            <span> ,-</span>
                        </div>
                    </td>
                    @endif
                </tr>
                @endforeach
                @else
                <tr>
                    <td>{{ $kategori_level_one['name'] }}</td>
                    @if ($ringkasan['type'] === 'UANG MASUK')
                    <td></td>
                    <td>
                        <div class="flex justify-between">
                            <span>Rp</span>
                            <span>{{ number_format($kategori_level_one['jumlah'],0,',','.') }}</span>
                            <span> ,-</span>
                        </div>
                    </td>
                    @elseif ($ringkasan['type'] === 'UANG KELUAR')
                    <td>
                        <div class="flex justify-between">
                            <span>Rp</span>
                            <span>{{ number_format($kategori_level_one['jumlah'],0,',','.') }}</span>
                            <span> ,-</span>
                        </div>
                    </td>
                    @endif
                </tr>
                @endif
                @endforeach
                @endforeach
            </table>
        </div>
        {{-- END - RINGKASANS --}}

        {{-- PRINTOUT RINGKASANS --}}
        <div class="mt-2">
            <table id="table-ringkasan" class="hidden">
                <tr>
                    <th></th>
                    <th>{{ $keluar_total }}</th>
                    <th>{{ $masuk_total }}</th>
                    <th>{{ $masuk_total - $keluar_total }}</th>
                </tr>
                <tr><th rowspan="2">KODE AKUN</th><th colspan="2">NERACA SALDO</th></tr>
                <tr><th>KELUAR</th><th>MASUK</th></tr>

                @foreach ($ringkasans as $key_ringkasan => $ringkasan)
                <tr>
                    @if ($ringkasan['type'] === 'UANG MASUK')
                    <th class="bg-emerald-300">{{ $ringkasan['type'] }}</th>
                    @elseif ($ringkasan['type'] === 'UANG KELUAR')
                    <th class="bg-yellow-300">{{ $ringkasan['type'] }}</th>
                    @endif
                </tr>
                @foreach ($ringkasan['kategori_level_one'] as $kategori_level_one)
                @if (isset($kategori_level_one['kategori_level_two']))
                <tr><td></td><td></td><td></td></tr>
                <tr><td colspan="3"><div class="font-bold">{{ $kategori_level_one['name'] }}</div></td></tr>
                @foreach ($kategori_level_one['kategori_level_two'] as $kategori_level_two)
                <tr>
                    <td>{{ $kategori_level_two['name'] }}</td>
                    @if ($ringkasan['type'] === 'UANG MASUK')
                    <td></td>
                    <td>{{ $kategori_level_two['jumlah'] }}</td>
                    @elseif ($ringkasan['type'] === 'UANG KELUAR')
                    <td>{{ $kategori_level_two['jumlah'] }}</td>
                    @endif
                </tr>
                @endforeach
                @else
                <tr>
                    <td>{{ $kategori_level_one['name'] }}</td>
                    @if ($ringkasan['type'] === 'UANG MASUK')
                    <td></td>
                    <td>{{ $kategori_level_one['jumlah'] }}</td>
                    @elseif ($ringkasan['type'] === 'UANG KELUAR')
                    <td>{{ $kategori_level_one['jumlah'] }}</td>
                    @endif
                </tr>
                @endif
                @endforeach
                @endforeach
            </table>
        </div>
        {{-- END - PRINTOUT RINGKASANS --}}
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
    function table_to_excel(table_id) {
        $(`#${table_id}`).table2excel({
            filename:`${table_id}.xls`
        });
    }
</script>

@endsection
