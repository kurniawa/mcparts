@extends('layouts.main')
@section('content')
<header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <h1 class="text-xl font-bold tracking-tight text-gray-900">Nota Pembelian: {{ $pembelian->supplier_nama }} - {{ $pembelian->nomor_nota }}</h1>
    </div>
  </header>
<main class="mb-9">
    <x-errors-any></x-errors-any>
    <x-validation-feedback></x-validation-feedback>

    {{-- FORM_EDIT_PEMBELIAN --}}
    <div id="form_new_pembelian">
        <div class="flex justify-center">
            <form action="{{ route('pembelians.update', $pembelian->id) }}" method="POST" class="border rounded border-emerald-300 p-1 mt-1 lg:w-3/5 md:w-3/4">
                @csrf
                <div class="border rounded p-2">
                    <div class="border-b pb-3">
                        <table>
                            <tr>
                                <td>Nomor</td><td><div class="mx-2">:</div></td>
                                <td><input type="text" name="nomor_nota" class="rounded p-1 text-xs" placeholder="nomor nota ..." value="{{ $pembelian->nomor_nota }}"></td>
                            </tr>
                            <tr>
                                <td>Tanggal</td><td><div class="mx-2">:</div></td>
                                <td class="py-1">
                                    <div class="flex">
                                        <input type="text" name="day" id="day" class="border rounded text-xs p-1 w-8" placeholder="dd" value="{{ date('d', strtotime($pembelian->created_at)) }}">
                                        <input type="text" name="month" id="month" class="border rounded text-xs p-1 w-8 ml-1" placeholder="mm" value="{{ date('m', strtotime($pembelian->created_at)) }}">
                                        <input type="text" name="year" id="year" class="border rounded text-xs p-1 w-11 ml-1" placeholder="yyyy" value="{{ date('Y', strtotime($pembelian->created_at)) }}">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Supplier</td><td><div class="mx-2">:</div></td>
                                <td class="py-1">
                                    <input type="text" name="supplier_nama" id="supplier_nama" placeholder="nama supplier..." class="text-xs rounded-md border-0 py-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600" value="{{ $pembelian->supplier_nama }}">
                                    <input type="hidden" name="supplier_id" id="supplier_id" value="{{ $pembelian->supplier_id }}">
                                </td>
                            </tr>
                            <tr class="align-top">
                                <td>Ket. (opt.)</td><td><div class="mx-2">:</div></td>
                                <td class="py-1">
                                    {{-- <input type="text" name="keterangan" placeholder="judul/keterangan..." class="text-xs rounded-md border-0 py-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600" value="{{ $pembelian->keterangan }}"> --}}
                                    <textarea name="keterangan" id="" cols="30" rows="5" class="border rounded p-1 text-xs" placeholder="keterangan (opt.)">{{ $pembelian->keterangan }}</textarea>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="mt-2">
                        <table id="table_pembelian_items" class="text-slate-500 w-full">
                            <tr><th>Nama Item</th><th>Jml. Sub</th><th>Jml. Main</th><th>Hrg.</th><th>Hrg. t</th><th></th></tr>
                            {{-- {{ dump($pembelian_barangs) }} --}}
                            @foreach ($pembelian_barangs as $key_pembelian_barang => $pembelian_barang)
                            <tr id="tr_barang-{{ $key_pembelian_barang }}">
                                <td>
                                    <div class="flex items-center mt-1">
                                        <button id="toggle_barang_keterangan-{{ $key_pembelian_barang }}" type="button" class="border border-yellow-500 rounded text-yellow-500" onclick="toggle_light(this.id,'barang_keterangan-{{ $key_pembelian_barang }}', [], ['bg-yellow-300'], 'block')">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-3 h-3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                        </button>
                                        <input type="text" name="barang_nama[]" id="barang_nama-{{ $key_pembelian_barang }}" value="{{ $pembelian_barang->barang_nama }}" class="border-slate-300 rounded-lg text-xs p-1 ml-1 placeholder:text-slate-400 w-56" placeholder="nama item...">
                                        <input type="hidden" name="barang_id[]" id="barang_id-{{ $key_pembelian_barang }}" value="{{ $pembelian_barang->barang_id }}">
                                        <input type="hidden" name="pembelian_barang_id[]" id="pembelian_barang_id-{{ $key_pembelian_barang }}" value="{{ $pembelian_barang->id }}">
                                    </div>
                                    <div class="mt-1 hidden" id="barang_keterangan-{{ $key_pembelian_barang }}">
                                        <textarea name="barang_keterangan[]" cols="30" rows="3" class="border-slate-300 rounded-lg text-xs p-0 placeholder:text-slate-400" placeholder="keterangan item..."></textarea>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-center">
                                        <div class="flex items-center">
                                            <input type="number" name="jumlah_sub[]" id="jumlah-sub-{{ $key_pembelian_barang }}" value="{{ $pembelian_barang->jumlah_sub }}" min="1" step="1" class="border-slate-300 rounded-lg text-xs p-1 w-1/2" oninput="countHargaTotal({{ $key_pembelian_barang }})">
                                            <span id="satuan_sub-{{ $key_pembelian_barang }}" class="ml-1">{{ $pembelian_barang->satuan_sub }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-center">
                                        <div class="flex items-center">
                                            <input type="number" name="jumlah_main[]" id="jumlah-main-{{ $key_pembelian_barang }}" value="{{ $pembelian_barang->jumlah_main }}" min="1" step="1" class="border-slate-300 rounded-lg text-xs p-1 w-1/2" oninput="countHargaTotal({{ $key_pembelian_barang }})">
                                            <span class="satuan_main-{{ $key_pembelian_barang }} ml-1">{{ $pembelian_barang->satuan_main }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-center">
                                        <div class="flex items-center">
                                            <input type="text" id="harga-main-{{ $key_pembelian_barang }}" value="{{ number_format($pembelian_barang->harga_main,2,',','.') }}" min="1" step="1" class="border-slate-300 rounded-lg text-xs p-1 w-3/4" onchange="formatNumber(this, 'harga-main-real-{{ $key_pembelian_barang }}'); countHargaTotal({{ $key_pembelian_barang }})">/<span class="satuan-main-{{ $key_pembelian_barang }} ml-1">{{ $pembelian_barang->satuan_main }}</span>
                                            <input type="hidden" name="harga_main[]" id="harga-main-real-{{ $key_pembelian_barang }}" value="{{ $pembelian_barang->harga_main }}">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-center">
                                        <div class="flex">
                                            <input type="text" id="harga-t-{{ $key_pembelian_barang }}" value="{{ number_format($pembelian_barang->harga_t,2,',','.') }}" min="1" step="1" class="border-slate-300 rounded-lg text-xs p-1 w-full" onchange="formatNumber(this, 'harga-t-real-{{ $key_pembelian_barang }}');">
                                            <input type="hidden" name="harga_t[]" id="harga-t-real-{{ $key_pembelian_barang }}" value="{{ $pembelian_barang->harga_t }}" class="harga-t-real">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <button type="button" class="text-red-500" onclick="remove_item({{ $key_pembelian_barang }})">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                            <tr id="tr_add_item">
                                <td>
                                    <button type="button" class="rounded bg-emerald-200 text-emerald-600" onclick="addItem('tr_add_item','table_pembelian_items')">
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
                                <input type="text" id="harga-total-pembelian" class="border-none p-1 w-28 ml-2" value="{{ number_format($pembelian->harga_total,2,',','.') }}" readonly>
                            </div>
                            <input type="hidden" name="harga_total" id="harga-total-pembelian-real" value="{{ $pembelian->harga_total }}">
                        </div>
                    </div>
                </div>
                <div class="flex justify-center mt-3">
                    <button type="submit" class="border-2 border-emerald-300 bg-emerald-200 text-emerald-600 rounded-lg font-semibold py-1 px-3 hover:bg-emerald-300">Proses/Konfirmasi</button>
                </div>
            </form>
        </div>
    </div>
    {{-- END - FORM_EDIT_PEMBELIAN --}}

</main>

<script>
    const labelSupplier = {!! json_encode($labelSupplier, JSON_HEX_TAG) !!}
    const labelBarang = {!! json_encode($labelBarang, JSON_HEX_TAG) !!}

    let supplierID = {{ $pembelian->supplier_id }};
    let indexItem = {{ count($pembelian_barangs) }};
</script>

<script src="{{ asset('js/pembelianHelper.js') }}"></script>

@endsection
