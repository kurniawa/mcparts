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
        {{-- <div class="flex">
            <button id="btn_filter" class="border rounded border-yellow-300 text-yellow-500 px-3 py-1" onclick="toggle_light(this.id,'filter-content',[],['bg-yellow-200'], 'block')">Filter</button>
        </div> --}}
        {{-- SEARCH / FILTER --}}
        <div class="mt-1 flex justify-center" id="filter-content">
            <x-filter-form :showCustomer="true"></x-filter-form>
        </div>
        {{-- END - SEARCH / FILTER --}}
        <div class="flex justify-center mt-2">
            <button id="btn-index-penjualan" type="button" class="p-2 border"
                onclick="show_hide(['index-penjualan'], ['piutang-penjualan'], [this.id], ['btn-piutang-penjualan'], ['bg-sky-200'], ['bg-sky-200'], 'block')">
                Index
            </button>
            <button id="btn-piutang-penjualan" type="button" class="p-2 border"
                onclick="show_hide(['piutang-penjualan'], ['index-penjualan'], [this.id], ['btn-index-penjualan'], ['bg-sky-200'], ['bg-sky-200'], 'block')">
                Piutang Penjualan
            </button>
        </div>

        <div id="index-penjualan" class="mt-2">
            <x-index-penjualan :itemPelanggans="$itemPelanggans" 
            :totalPenjualanPelangganAll="$totalPenjualanPelangganAll"
            :grandTotal="$grandTotal"
            :notaSubtotalAll="$notaSubtotalAll"
            :notaDetailItemsAll="$notaDetailItemsAll"
            >
            </x-index-penjualan>
        </div>

        <div id="piutang-penjualan">
            <x-piutang-penjualan
                :itemPelanggans="$itemPelanggans"
                :totalPenjualanPelangganAllForPiutang="$totalPenjualanPelangganAllForPiutang"
                :grandTotalForPiutang="$grandTotalForPiutang"
                :notaSubtotalAllForPiutang="$notaSubtotalAllForPiutang"
                :notaDetailItemsAllForPiutang="$notaDetailItemsAllForPiutang"
                >
            </x-piutang-penjualan>
        </div>
    </div>
        
    {{-- VERSI UNTUK DOWNLOAD --}}
    <table id="nota_subtotal_download" class="hidden">
        <tr><th>No.</th><th>Tanggal</th><th>Pelanggan</th><th>Harga</th><th>Subtotal</th></tr>
        @foreach ($notaSubtotalAll as $nota_subtotal)
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
        @foreach ($notaDetailItemsAll as $nota_detail_item)
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
    {{-- Supaya background terbaca oleh web --}}
    <div class="bg-orange-50"></div>
    <div class="bg-sky-100"></div>
    <div class="bg-green-100"></div>
    <div class="bg-red-100"></div>
    <div class="bg-orange-100"></div>
    <div class="bg-green-200"></div>
    <div class="bg-red-200"></div>
    <div class="bg-orange-200"></div>
</main>

<script>
    function table_to_excel(table_id) {
        $(`#${table_id}`).table2excel({
            filename:`${table_id}.xls`
        });
    }

    function show_hide(id_to_show, id_to_hide, id_btn_show, id_btn_hide, class_btn_show, class_btn_hide, display_style) {
        id_to_show.forEach(element => {
            const el = document.getElementById(element);
            if (el) {
                el.style.display = display_style;
            }
        });
        id_to_hide.forEach(element => {
            const el = document.getElementById(element);
            if (el) {
                el.style.display = 'none';
            }
        });
        id_btn_show.forEach(element => {
            const el = document.getElementById(element);
            if (el) {
                el.classList.add(...class_btn_show);
            }
        });
        id_btn_hide.forEach(element => {
            const el = document.getElementById(element);
            if (el) {
                el.classList.remove(...class_btn_hide);
            }
        });
    }
</script>

@endsection
