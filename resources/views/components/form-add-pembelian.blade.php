{{-- FORM_NEW_PEMBELIAN --}}
<div>
    <div id="form-new-pembelian" class="hidden">
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
                                    <input type="text" name="supplier_nama" id="pembelian-new-supplier-nama" placeholder="nama supplier..." class="text-xs rounded-md border-0 py-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600">
                                    <input type="hidden" name="supplier_id" id="pembelian-new-supplier-id">
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
                        <table id="table-pembelian-items" class="text-slate-500 w-full">
                            <tr><th>Nama Item</th><th>Jml. Sub</th><th>Jml. Main</th><th>Hrg.</th><th>Hrg. t</th><th></th></tr>
                            <tr id="tr-add-item">
                                <td>
                                    <button type="button" class="rounded bg-emerald-200 text-emerald-600" onclick="addItem('tr-add-item','table-pembelian-items')">
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
                                <input type="text" id="harga-total-pembelian" class="border-none p-1 w-28 ml-2" readonly>
                                <span class="ml-1">,-</span>
                            </div>
                            <input type="hidden" name="harga_total" id="harga-total-pembelian-real">
                        </div>
                    </div>
                </div>
                <div class="flex justify-center mt-3">
                    <button type="submit" class="border-2 border-emerald-300 bg-emerald-200 text-emerald-600 rounded-lg font-semibold py-1 px-3 hover:bg-emerald-300">Proses/Konfirmasi</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const labelSupplier = {!! json_encode($labelSupplier, JSON_HEX_TAG) !!}
        const labelBarang = {!! json_encode($labelBarang, JSON_HEX_TAG) !!}

        let supplierID = 0;
        let indexItem = 0;
        let mode = 'new';
    </script>

    <script src="{{ asset('js/pembelianHelper.js') }}"></script>
</div>

