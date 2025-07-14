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
        <div class="flex items-center">
            <h1 class="text-xl font-bold">Laporan Laba-Rugi</h1>
            {{-- <div class="ml-2">
                <button onclick="table_to_excel('laba_rugi_table')" class="bg-violet-400 text-white font-semibold p-1 rounded">Export to Excel</button>
            </div> --}}
        </div>
        <div class="mt-2">
            <x-filter-form :action="route('accounting.laba_rugi')" :showCustomer="false"></x-filter-form>
        </div>
        <div class="mt-2">
            <table class="table-nice">
                <thead>
                    <tr>
                        <th>KODE AKUN</th>
                        <th>NAMA AKUN</th>
                    </tr>
                </thead>
                <tr>
                    <td></td>
                    <td class="font-bold">PENDAPATAN</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="text-left">PENJUALAN BARANG DAN JASA</td>
                    <td>{{ number_format($penjualan_barang_dan_jasa, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
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
