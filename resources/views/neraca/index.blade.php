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
                <h1 class="text-xl font-bold">ESTIMASI NERACA</h1>
                <h2 class="text-xl font-bold">MC-PARTS .CV</h2>
                <h2 class="text-xl font-bold">{{ date('d M Y', strtotime($from)) }} - {{ date('d M Y', strtotime($until)) }}</h2>
            </div>
            <x-filter-form :action="route('neraca.index')" :showCustomer="false"></x-filter-form>
        </div>

        {{-- RINGKASANS --}}
        <div class="mt-2">
            <div class="flex gap-2 items-start">
                <table class="text-xs">
                    <tr>
                        <th>
                            <div class="flex justify-start">
                                <button class="rounded bg-emerald-400 text-white p-1" onclick="table_to_excel('table-estimasi-neraca')">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 13.5l3 3m0 0l3-3m-3 3v-6m1.06-4.19l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                                    </svg>
                                </button>
                            </div>
                        </th>
                    </tr>
                    <tr><th colspan="2">ESTIMASI ASET</th></tr>
                    <tr>{{-- Kosong 1 baris --}}</tr>
                    <tr><td class="font-bold">ASET LANCAR</td></tr>
                    @foreach ($aset_lancar as $asetLancar)
                    @if ($asetLancar['amount'] > 0)
                    <tr>
                        <td>
                            {{ $asetLancar['shown_name'] }}
                        </td>
                        <td>{{ number_format($asetLancar['amount'], 2, ',', '.') }}</td>
                    </tr>
                    @endif
                    @endforeach
                    <tr><td></td><td></td></tr>
                    <tr><td></td><td></td></tr>
                    <tr><td></td><td></td></tr>
                    <tr><td class="font-bold text-red-500">TOTAL ASET LANCAR</td><td class="font-bold text-red-500">{{ number_format($total_aset_lancar, 2, ',', '.') }}</td></tr>
                    <tr><td></td><td></td></tr>
                    <tr><td></td><td></td></tr>
                    <tr><td class="font-bold">ASET TETAP</td><td></td></tr>
                    @foreach ($aset_tetap as $asetTetap)
                    <tr>
                        <td>{{ $asetTetap['shown_name'] }}</td>
                        <td>{{ number_format($asetTetap['amount'], 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    <tr><td></td><td></td></tr>
                    <tr><td class="font-bold text-red-500">TOTAL ASET TETAP</td><td class="font-bold text-red-500">{{ number_format($total_aset_tetap, 2, ',', '.') }}</td></tr>
                    <tr><td></td><td></td></tr>
                    <tr><td></td><td></td></tr>
                    <tr><td class="font-bold text-red-500">ESTIMASI TOTAL ASET</td><td class="font-bold text-red-500">{{ number_format($estimasi_total_aset, 2, ',', '.') }}</td></tr>
                </table>

                <table class="text-xs">
                    <tr>
                        <th>
                            <div class="flex justify-start">
                                <button class="rounded bg-emerald-400 text-white p-1" onclick="table_to_excel('table-estimasi-neraca')">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 13.5l3 3m0 0l3-3m-3 3v-6m1.06-4.19l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                                    </svg>
                                </button>
                            </div>
                        </th>
                    </tr>
                    <tr><th colspan="2">ESTIMASI KEWAJIBAN + MODAL</th></tr>
                    <tr>{{-- Kosong 1 baris --}}</tr>
                    <tr><td class="font-bold">HUTANG</td></tr>
                    <tr><td>HUTANG DAGANG</td><td>{{ number_format($account_payable, 2, ',', '.') }}</td></tr>
                    <tr><td></td><td></td></tr>
                    <tr><td></td><td></td></tr>
                    <tr class="font-bold text-red-500"><td>TOTAL HUTANG</td><td>{{ number_format($account_payable, 2, ',', '.') }}</td></tr>
                </table>

                <table class="text-xs">
                    <tr>
                        <th>
                            <div class="flex justify-start">
                                <button class="rounded bg-emerald-400 text-white p-1" onclick="table_to_excel('table-estimasi-neraca')">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 13.5l3 3m0 0l3-3m-3 3v-6m1.06-4.19l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                                    </svg>
                                </button>
                            </div>
                        </th>
                    </tr>
                    @foreach ($kas_kantor as $kasKantor)
                    <tr>
                        <td>{{ $kasKantor['shown_name'] }}</td>
                        <td>{{ number_format($kasKantor['amount'], 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </table>

            </div>
        </div>
        {{-- END - RINGKASANS --}}

    </div>
</main>

<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
    td {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
        padding-top: 0.2rem;
        padding-bottom: 0.2rem;
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
