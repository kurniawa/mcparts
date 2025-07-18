<div>
    {{-- ITEM YANG BIASA DIAMBIL PELANGGAN --}}
    @if ($itemPelanggans)
    <div class="mt-2">
        <div class="flex justify-center">
            <div class="bg-white rounded shadow drop-shadow p-1">
                <h3 class="font-semibold ml-2">Barang Yang Pernah Di Order Pada Rentang Waktu Terpilih</h3>
            </div>
        </div>
        <div class="flex justify-center">
            <table class="table-nice mt-1">
                <tr><th>Tanggal</th><th>Nota</th><th>Nama Item</th><th>Jml.</th><th>Harga</th><th>Total</th></tr>
                @foreach ($itemPelanggans as $items)
                @foreach ($items as $item)
                <tr>
                    <td>{{ date('d-m-Y', strtotime($item->created_at)) }}</td>
                    <td>N-{{ $item->nota_id }}</td>
                    <td>{{ $item->nama_nota }}</td>
                    <td>{{ $item->jumlah }}</td>
                    <td>{{ number_format($item->harga,0,',','.') }}</td>
                    <td>{{ number_format($item->harga_t,0,',','.') }}</td>
                </tr>
                @endforeach
                @endforeach
            </table>
        </div>
    </div>
    @endif
    {{-- END - ITEM YANG BIASA DIAMBIL PELANGGAN --}}
    <div class="flex flex-col md:flex-row gap-2 mt-2 justify-center">
        {{-- TOTAL_PENJUALAN_PELANGGAN --}}
        <div>
            <div class="flex items-center">
                <div class="flex items-center bg-white rounded shadow drop-shadow p-1">
                    <h3 class="font-semibold ml-2">Total Penjualan Pelanggan</h3>
                </div>
            </div>
            <table class="table-nice mt-1">
                <tr><th>No.</th><th>Customer</th><th>Total Penjualan</th></tr>
                @foreach ($totalPenjualanPelangganAllForPiutang as $key_total_penjualan => $total_penjualan_pelanggan)
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
                            {{ number_format($grandTotalForPiutang,0,',','.') }},-
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        {{-- END - TOTAL_PENJUALAN_PELANGGAN --}}
        <div>
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
                    @foreach ($notaSubtotalAllForPiutang as $nota_subtotal)
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
                    @foreach ($notaDetailItemsAllForPiutang as $nota_detail_item)
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