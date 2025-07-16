<div id="{{ isset($id) ? $id : '' }}" class="{{ isset($class) ? $class : '' }}">
    <form action="{{ route('barangs.update', $barang->id) }}" method="POST" class="border rounded border-indigo-300 p-1 mt-1 w-full">
        @csrf
        <table class="text-xs w-full">
            <tr>
                <td>Supplier</td><td><div class="mx-2">:</div></td>
                <td class="py-1">
                    <input type="text" name="supplier_nama" id="supplier_nama" value="{{ $barang->supplier_nama }}" placeholder="nama supplier..." class="text-xs rounded-md border-0 py-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600">
                    <input type="hidden" name="supplier_id" id="supplier_id" value="{{ $barang->supplier_id }}">
                </td>
            </tr>
            <tr>
                <td>Nama</td><td><div class="mx-2">:</div></td>
                <td>
                    <input type="text" name="barang_nama" id="barang_nama" value="{{ $barang->nama }}" placeholder="nama barang ..." class="w-full text-xs rounded-md border-0 py-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600">
                    <input type="hidden" name="barang_id" id="barang_id" value="{{ $barang->id }}">
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <div class="my-5 border rounded p-1 border-sky-500">
                        <div class="my-2 font-semibold text-center">Satuan - Jumlah - Harga per Satuan - Harga Total:</div>
                        <table class="w-full">
                            <tr>
                                <td>Satuan Utama</td><td><div class="mx-1">:</div></td><td><input type="text" name="satuan_main" value="{{ $barang->satuan_main }}" class="text-xs rounded p-1 w-3/4"></td>
                                <td>Jumlah</td><td><div class="mx-1">:</div></td>
                                <td>
                                    <input type="number" name="jumlah_main" id="jumlah_main" value="{{ $barang->jumlah_main / 100 }}" class="text-xs rounded p-1 w-3/4" oninput="count_harga_total_main()">
                                </td>
                                <td>Harga</td><td><div class="mx-1">:</div></td>
                                <td>
                                    <input type="text" id="harga_main" value="{{ number_format($barang->harga_main,0,',','.') }}" class="text-xs rounded p-1" onchange="formatNumber(this, 'harga_main-real');count_harga_total_main()">
                                    <input type="hidden" name="harga_main" id="harga_main-real" value="{{ $barang->harga_main }}">
                                </td>
                                <td>Harga Total</td><td><div class="mx-1">:</div></td>
                                <td>
                                    <input type="text" name="harga_total_main" id="harga_total_main" value="{{ number_format($barang->harga_total_main,0,',','.') }}" class="text-xs rounded p-1" onchange="formatNumber(this, 'harga_total_main-real');copy_to_harga_sub();count_harga_total_sub()">
                                    <input type="hidden" name="harga_total_main" id="harga_total_main-real" value="{{ $barang->harga_total_main }}">
                                </td>
                            </tr>
                            <tr>
                                <td>Satuan Sub</td><td><div class="mx-1">:</div></td><td><input type="text" name="satuan_sub" value="{{ $barang->satuan_sub }}" class="text-xs rounded p-1 w-3/4"></td>
                                <td>Jumlah</td><td><div class="mx-1">:</div></td>
                                <td>
                                    <input type="number" name="jumlah_sub" id="jumlah_sub" value="{{ $barang->jumlah_sub / 100 }}" class="text-xs rounded p-1 w-3/4" oninput="count_harga_total_sub()">
                                </td>
                                <td>Harga</td><td><div class="mx-1">:</div></td>
                                <td>
                                    <input type="text" id="harga_sub" value="{{ number_format($barang->harga_sub,0,',','.') }}" class="text-xs rounded p-1" onchange="formatNumber(this, 'harga_sub-real');count_harga_total_sub()">
                                    <input type="hidden" name="harga_sub" id="harga_sub-real" value="{{ $barang->harga_sub }}">
                                </td>
                                <td>Harga Total</td><td><div class="mx-1">:</div></td>
                                <td>
                                    <input type="text" name="harga_total_sub" id="harga_total_sub" value="{{ number_format($barang->harga_total_sub,0,',','.') }}" class="text-xs rounded p-1" onchange="formatNumber(this, 'harga_total_sub-real');">
                                    <input type="hidden" name="harga_total_sub" id="harga_total_sub-real" value="{{ $barang->harga_total_sub }}">
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
            <tr class="align-top">
                <td>Ket. (opt.)</td><td><div class="mx-2">:</div></td>
                <td class="py-1"><textarea name="keterangan" id="" cols="40" rows="3" placeholder="keterangan..." class="rounded text-xs p-1">{{ $barang->keterangan }}</textarea></td>
            </tr>
        </table>
        <div class="flex justify-center mt-3">
            <button type="submit" class="border-2 border-indigo-300 bg-indigo-200 text-indigo-600 rounded-lg font-semibold py-1 px-3 hover:bg-indigo-300">Confirm Edit</button>
        </div>
    </form>
</div>