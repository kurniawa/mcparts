@extends('layouts.main')
@section('content')
<header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <h1 class="text-xl font-bold tracking-tight text-gray-900">Detail Barang/Item</h1>
      <div class="flex gap-2 items-center">
        <span>Nama Barang/Item:</span><span class="font-bold text-lg text-gray-600">{{ $barang->nama }}</span>
      </div>
    </div>
  </header>
<main class="mb-9 relative">
    <x-errors-any></x-errors-any>
    <x-validation-feedback></x-validation-feedback>

    

    <div class="flex justify-center gap-2 flex-col md:flex-row mb-5 mx-2">
        {{-- PHOTO DISPLAY --}}
        {{-- <x-photo-display :defaultPhoto="$defaultPhoto" :subsidiaryPhotos="$subsidiaryPhotos" :item="$barang"></x-photo-display> --}}
        {{-- DATA BARANG --}}
        <div class="bg-white rounded p-2 shadow drop-shadow mx-2">
            <div class="text-xs">
                Supplier: {{ $barang->supplier_nama }}<br>
                Nama Barang/Item: {{ $barang->nama }}<br>
                Satuan Utama: {{ $barang->satuan_main }} - Jumlah Utama: {{ $barang->jumlah_main / 100 }} - Harga Utama: {{ number_format($barang->harga_main,0,',','.') }}<br>
                Satuan Sub: {{ $barang->satuan_sub }} - Jumlah Sub: {{ $barang->jumlah_sub / 100 }} - Harga Sub: {{ number_format($barang->harga_sub,0,',','.') }}<br>
                Keterangan: <br>
                <textarea name="" id="" cols="30" rows="3" class="border text-xs p-1">{{ $barang->keterangan }}</textarea>
            </div>
            <div class="flex justify-center mt-2">
                <button type="submit" class="border rounded border-slate-300 text-slate-500 font-semibold ml-1 flex justify-center items-center gap-1 p-2 text-xs" id="btn-goods-edit" onclick="toggle_light(this.id, 'goods-edit-form', [], ['bg-slate-200'], 'block')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                    <span>Edit Barang</span>
                </button>
            </div>
            <form action="{{ route('barangs.delete', $barang->id) }}" method="POST" class="flex justify-center mt-2 text-xs" onsubmit="return confirm('Yakin hapus produk?')">
                @csrf
                <button class="p-1 border-2 border-pink-300 rounded text-pink-500 bg-pink-200 flex gap-1 items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                    </svg>
                    <span>Hapus Barang</span>
                </button>
            </form>

            {{-- FORM EDIT_PRODUK --}}
            <x-goods-edit-form :barang="$barang" id="goods-edit-form" class="hidden"></x-goods-edit-form>
            {{-- END - FORM EDIT_PRODUK --}}
        </div>

    </div>

    {{-- VIEW IMAGE/PHOTO --}}
    @if ($defaultPhoto !== null)
    <div id="close_layer" class="absolute -top-28 right-0 -bottom-28 left-0 bg-slate-700 opacity-80 hidden" onclick="closeViewImage('default_image', 'close_layer')"></div>
    <div id="default_image" class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 hidden">
        <div class="w-screen h-auto md:w-5/6 md:h-5/6 bg-white p-3 rounded-lg">
            <img id="preview_photo" src={{ asset("storage/$defaultPhoto->path") }} class="w-full">
        </div>

        
        <div class="flex gap-1 items-center">
            <form action="{{ route('produk_photos.delete_photo', [$barang->id, $barang_photo_default->id, $defaultPhoto->id]) }}" onsubmit="return confirm('Yakin ingin menghapus foto ini?')" method="POST">
                @csrf
                <button type="submit" class="px-2 py-1 font-semibold text-xs bg-pink-500 text-white hover:bg-pink-600 active:bg-pink-700 focus:ring focus:ring-pink-300 rounded mt-1 flex justify-center gap-1 items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                    <span>Delete</span>
                </button>
            </form>
            <form action="{{ route('produk_photos.jadikan_subsidiary', [$barang->id, $barang_photo_default->id]) }}" onsubmit="return confirm('Jadikan foto ini sebagai subsidiary?')" method="POST">
                @csrf
                <button type="submit" class="px-2 py-1 font-semibold text-xs bg-emerald-500 text-white hover:bg-emerald-600 active:bg-emerald-700 focus:ring focus:ring-emerald-300 rounded mt-1 flex justify-center gap-1 items-center">
                    <span>Jadikan Sub</span>
                </button>
            </form>
        </div>
    </div>
    @endif

    @foreach ($subsidiaryPhotos as $key => $sub_photo)
    <div id="close_layer-{{ $key }}" class="absolute -top-28 right-0 -bottom-28 left-0 bg-slate-700 opacity-80 hidden" onclick="closeViewImage('sub_image-{{ $key }}', 'close_layer-{{ $key }}')"></div>
    <div id="sub_image-{{ $key }}" class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 hidden">
        <div class="w-screen h-auto md:w-5/6 md:h-5/6 bg-white p-3 rounded-lg">
            <img id="preview_photo-{{ $key }}" src={{ asset("storage/$sub_photo->path") }} class="w-full">
        </div>
        
        <div class="flex gap-1 items-center">
            <form action="{{ route('produk_photos.delete_photo', [$barang->id, $barang_photo_subsidiary[$key]->id, $sub_photo->id]) }}" onsubmit="return confirm('Yakin ingin menghapus foto ini?')" method="POST">
                @csrf
                <button type="submit" class="px-2 py-1 font-semibold text-xs bg-pink-500 text-white hover:bg-pink-600 active:bg-pink-700 focus:ring focus:ring-pink-300 rounded mt-1 flex justify-center gap-1 items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                    <span>Delete</span>
                </button>
            </form>
            <form action="{{ route('produk_photos.jadikan_default', [$barang->id, $barang_photo_subsidiary[$key]->id]) }}" onsubmit="return confirm('Jadikan foto ini sebagai default?')" method="POST">
                @csrf
                <button type="submit" class="px-2 py-1 font-semibold text-xs bg-emerald-500 text-white hover:bg-emerald-600 active:bg-emerald-700 focus:ring focus:ring-emerald-300 rounded mt-1 flex justify-center gap-1 items-center">
                    <span>Jadikan Default</span>
                </button>
            </form>
        </div>
    </div>
    @endforeach
    {{-- END - VIEW IMAGE/PHOTO --}}

    <div class="flex gap-2 mt-2 justify-center">
        <x-price-chart :priceChartData="$priceChartData"></x-price-chart>
        <div>
            <div class="text-center">
                <h2 class="font-bold">Data Pembelian Terkait</h2>
            </div>
            <table class="text-xs" id="table-data-pembelian-terkait">
                <tr>
                    <th>Tanggal</th>
                    <th>Tanggal Lunas</th>
                    <th>Supplier</th>
                    <th>Nota</th>
                    <th>Jumlah</th>
                    <th>Harga Satuan</th>
                    <th>Harga Total</th>
                </tr>
                @foreach ($pembelians as $key => $pembelian)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($pembelian->created_at)->format('d M Y') }}</td>
                    <td>{{ $pembelian->tanggal_lunas ? \Carbon\Carbon::parse($pembelian->tanggal_lunas)->format('d M Y') : '-' }}</td>
                    <td>{{ $pembelian->supplier_nama }}</td>
                    <td class="font-bold text-sky-500"><a href="{{ route('pembelians.show', $pembelian->id) }}">{{ $pembelian->nomor_nota }}</a></td>
                    <td>{{ $pembelians_barangs[$key]->jumlah_main / 100 }} {{ $pembelians_barangs[$key]->satuan_main }} ; {{ $pembelians_barangs[$key]->jumlah_sub / 100 }} {{ $pembelians_barangs[$key]->satuan_sub }}</td>
                    <td>{{ number_format($pembelians_barangs[$key]->harga_main,0,',','.') }}</td>
                    <td>{{ number_format($pembelians_barangs[$key]->harga_t,0,',','.') }}</td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>

</main>

<style>
    #table-data-pembelian-terkait,
    #table-data-pembelian-terkait th,
    #table-data-pembelian-terkait td {
        border: 1px solid black;
        border-collapse: collapse;
    }

    #table-data-pembelian-terkait th,
    #table-data-pembelian-terkait td {
        padding: 3px;
    }
</style>

<script>
    const label_supplier = {!! json_encode($label_supplier, JSON_HEX_TAG) !!}

    $("#supplier_nama").autocomplete({
        source: label_supplier,
        select: function(event, ui) {
            $("#supplier_id").val(ui.item.id);
        }
    });


    const label_produk = {!! json_encode($label_produk, JSON_HEX_TAG) !!};

    $(`#produk_nama`).autocomplete({
        source: label_produk,
        select: function (event, ui) {
            document.getElementById(`produk_nama-${index}`).value = ui.item.value;
        }
    });

    function closeViewImage(show_image_id, close_layer_id) {
        $(`#${close_layer_id}`).hide(300);
        $(`#${show_image_id}`).hide(300);
    }

    function showViewImage(show_image_id, close_layer_id) {
        $(`#${close_layer_id}`).show(300);
        $(`#${show_image_id}`).show(300);
    }

    function previewImage(image_file, preview_id) {
        if (image_file) {
            // console.log(image_file)
            document.getElementById(preview_id).src = URL.createObjectURL(image_file);
        }
    }

</script>

@endsection
