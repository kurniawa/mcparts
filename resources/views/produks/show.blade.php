@extends('layouts.main')
@section('content')
<header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <h1 class="text-xl font-bold tracking-tight text-gray-900">Detail Produk</h1>
      <div class="flex gap-2 items-center">
        <span>Nama Produk:</span><span class="font-bold text-lg text-gray-600">{{ $produk->nama }}</span>
      </div>
    </div>
  </header>
<main class="mb-9 relative">
    <x-errors-any></x-errors-any>
    <x-validation-feedback></x-validation-feedback>

    <div class="flex justify-center gap-2 flex-col md:flex-row mb-5 mx-2">
        <div>
            {{-- PHOTO DEFAULT --}}
            <div class="flex justify-center">
                @if ($default_photo !== null)
                <div class="md:w-96 md:h-96">
                    <button type="button" class="rounded-lg overflow-hidden bg-orange-100" onclick="showViewImage('default_image', 'close_layer')">
                        <img class="w-full" src="{{ asset("storage/$default_photo->path") }}">
                    </button>
                </div>
                @else
                <div class="w-72 h-72 md:w-96 md:h-96">
                    <div class="rounded-full overflow-hidden bg-orange-100 flex">
                        <button type="button" href="https://www.flaticon.com/free-icons/superhero" title="superhero icons" target="_blank" rel="noopener noreferrer" onclick="showViewImage('default_image', 'close_layer')">
                            <img class="object-cover" src="{{ asset('images/badger.png') }}" alt="Profile Picture">
                        </button>
                    </div>
                </div>
                @endif
            </div>
            {{-- END - PHOTO DEFAULT --}}

            {{-- PHOTO SUBSIDIARY --}}
            <div class="grid grid-cols-4 md:w-96 h-auto mt-2 border p-1 rounded gap-1">
                @if (count($subsidiary_photos) > 0)
                @foreach ($subsidiary_photos as $key => $sub_photo)
                <button type="button" class="rounded-lg overflow-hidden" onclick="showViewImage('sub_image-{{ $key }}', 'close_layer-{{ $key }}')">
                    <img src="{{ asset("storage/$sub_photo->path") }}" alt="" class="w-full">
                </button>
                @endforeach
                @endif
            </div>
            {{-- END - PHOTO SUBSIDIARY --}}

            {{-- TAMBAH PHOTO BARU --}}
            <div class="flex justify-center mt-3">
                <div class="border rounded-lg border-rose-300 p-2">
                    <div class="text-center">
                        <h3 class="text-lg font-bold">Tambah Foto Baru</h3>
                    </div>
                    <form action="{{ route('produks.store_photo', $produk->id) }}" method="POST" class="mt-2" enctype="multipart/form-data">
                        @csrf
                        <div class="flex justify-center">
                            <div class="w-5/6 h-auto">
                                <img id="preview_photo_baru" class="w-full">
                            </div>
                        </div>
                        <div class="mt-3">
                            <input type="file" name="photo" value="photo" onchange="previewImage(this.files[0], 'preview_photo_baru')">
                        </div>
                        <div class="flex justify-center text-xs mt-2">
                            <button type="submit" class="rounded p-2 bg-blue-500 font-semibold text-white">tambah foto</button>
                        </div>
                    </form>
                </div>
            </div>
            {{-- END - TAMBAH PHOTO BARU --}}

        </div>


        {{-- DATA PRODUK --}}
        <div class="bg-white rounded p-2 shadow drop-shadow mx-2">
            <div class="flex justify-center">
                <div>
                    <table class="text-xs">
                        <tr>
                            <td class="font-bold">Supplier</td><td><div class="mx-2">:</div></td>
                            <td class="py-1">{{ $produk->supplier_nama }}</td>
                            <td class="font-bold">Tipe Produk</td><td><div class="mx-2">:</div></td>
                            <td>{{ $produk->tipe }}</td>
                        </tr>
                        <tr>
                            <td class="font-bold">Nama</td><td><div class="mx-2">:</div></td><td>{{ $produk->nama }}</td>
                            <td class="font-bold">Harga</td><td><div class="mx-2">:</div></td>
                            <td>
                                <div class="flex justify-between">
                                    <span>Rp</span>
                                    <span>{{ number_format($produk_harga->harga,0,',','.') }}</span>
                                    <span>,-</span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-bold">Nama Nota</td><td><div class="mx-2">:</div></td><td>{{ $produk->nama_nota }}</td>
                            <td class="font-bold">Tipe Packing</td><td><div class="mx-2">:</div></td><td>{{ $produk->tipe_packing }}</td>
                        </tr>
                        <tr class="align-top">
                            <td class="font-bold">Keterangan</td><td><div class="mx-2">:</div></td>
                            <td class="py-1">
                                <textarea cols="20" rows="3" class="border rounded p-1 text-xs" readonly>{{ $produk->keterangan }}</textarea>
                            </td>
                            <td class="font-bold">Aturan Packing</td><td><div class="mx-2">:</div></td><td>{{ $produk->aturan_packing }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="flex justify-center">
                <button type="submit" class="border rounded border-slate-300 text-slate-500 font-semibold ml-1 flex justify-center items-center gap-1 p-2 text-xs" id="btn_edit_produk" onclick="toggle_light(this.id, 'form_edit_produk', [], ['bg-slate-200'], 'block')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                    <span>Edit Produk</span>
                </button>
            </div>
            <form action="{{ route('produks.delete', $produk->id) }}" method="POST" class="flex justify-center mt-2 text-xs" onsubmit="return confirm('Yakin hapus produk?')">
                @csrf
                <button class="p-1 border-2 border-pink-300 rounded text-pink-500 bg-pink-200 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                    </svg>
                    <span>Hapus Produk</span>
                </button>
            </form>

            {{-- FORM EDIT_PRODUK --}}
            <div id="form_edit_produk" class="hidden text-xs">
                <form action="{{ route('produks.update', $produk->id) }}" method="POST" class="border rounded border-emerald-300 p-1 mt-1">
                    @csrf
                    <div class="border rounded p-2 flex flex-col md:flex-row gap-1 text-xs">
                        <table>
                            <tr>
                                <td>Supplier</td><td><div class="mx-2">:</div></td>
                                <td class="py-1">
                                    <input type="text" name="supplier_nama" id="supplier_nama" value="{{ $produk->supplier_nama }}" placeholder="nama supplier..." class="text-xs rounded-md border-0 py-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600">
                                    <input type="hidden" name="supplier_id" id="supplier_id" value="{{ $produk->supplier_id }}">
                                </td>
                            </tr>
                            <tr>
                                <td>Nama</td><td><div class="mx-2">:</div></td>
                                <td><input type="text" name="nama" id="produk_nama" value="{{ $produk->nama }}" class="rounded p-1 text-xs w-full" oninput="generate_nama_nota(this.value)" placeholder="Nama Produk ..."></td>
                            </tr>
                            <tr>
                                <td>Nama Nota</td><td><div class="mx-2">:</div></td><td><input type="text" name="nama_nota" id="nama_nota" value="{{ $produk->nama_nota }}" class="rounded p-1 text-xs w-full" placeholder="Nama Nota ..."></td>
                            </tr>
                            <tr class="align-top">
                                <td>Ket. (opt.)</td><td><div class="mx-2">:</div></td>
                                <td class="py-1">
                                    {{-- <input type="text" name="keterangan" placeholder="judul/keterangan..." class="text-xs rounded-md border-0 py-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600"> --}}
                                    <textarea name="keterangan" id="" cols="30" rows="5" class="border rounded p-1 text-xs" placeholder="keterangan (opt.)">{{ $produk->keterangan }}</textarea>
                                </td>

                            </tr>
                        </table>
                        <table>
                            <tr>
                                <td>Tipe Produk</td><td><div class="mx-2">:</div></td>
                                <td>
                                    <select name="tipe" id="tipe" class="text-xs py-0 rounded">
                                        @foreach ($types as $tipe)
                                        @if ($produk->tipe === $tipe['tipe'])
                                        <option value="{{ $tipe['tipe'] }}" selected>{{ $tipe['tipe'] }}</option>
                                        @else
                                        <option value="{{ $tipe['tipe'] }}">{{ $tipe['tipe'] }}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Harga</td><td><div class="mx-2">:</div></td>
                                <td>
                                    <input type="text" id="harga" class="rounded p-1 text-xs" value="{{ number_format($produk_harga->harga,0,',','.') }}" onchange="formatNumber(this, 'harga_real')" placeholder="Harga ...">
                                    <input type="hidden" name="harga" id="harga_real" value="{{ $produk_harga->harga }}">
                                    <input type="hidden" name="produk_harga_id" value="{{ $produk_harga->id }}">
                                </td>
                            </tr>
                            <tr>
                                <td>Tipe Packing</td><td><div class="mx-2">:</div></td>
                                <td>
                                    <select name="tipe_packing" id="tipe_packing" class="text-xs py-0 rounded">
                                        @foreach ($tipe_packing as $tp)
                                        @if ($produk->tipe_packing === $tp)
                                        <option value="{{ $tp }}" selected>{{ $tp }}</option>
                                        @else
                                        <option value="{{ $tp }}">{{ $tp }}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Aturan Packing</td><td><div class="mx-2">:</div></td><td><input type="number" name="aturan_packing" id="aturan_packing" value="{{ $produk->aturan_packing }}" class="rounded p-1 text-xs" placeholder="Aturan Packing ..."></td>
                            </tr>
                        </table>

                    </div>
                    <div class="flex justify-center mt-3">
                        <button type="submit" class="border-2 border-emerald-300 bg-emerald-200 text-emerald-600 rounded-lg font-semibold py-1 px-3 hover:bg-emerald-300">confirm edit</button>
                    </div>
                </form>
            </div>
            {{-- END - FORM EDIT_PRODUK --}}
        </div>
        {{-- END - DATA PRODUK --}}


    </div>

    {{-- VIEW IMAGE/PHOTO --}}
    <div id="close_layer" class="absolute -top-28 right-0 -bottom-28 left-0 bg-slate-700 opacity-80 hidden" onclick="closeViewImage('default_image', 'close_layer')"></div>
    <div id="default_image" class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 hidden">
        @if ($default_photo !== null)
        <form action="{{ route('produks.update_photo', [$produk->id, $default_photo->id]) }}" method="POST" class="mt-3 text-center" enctype="multipart/form-data">
        @else
        <form action="{{ route('produks.store_photo', $produk->id) }}" method="POST" class="mt-3 text-center" enctype="multipart/form-data">
        @endif
            @csrf
            <div class="w-screen h-auto md:w-5/6 md:h-5/6 bg-white p-3 rounded-lg">
                @if ($default_photo !== null)
                <img id="preview_photo" src={{ asset("storage/$default_photo->path") }} class="w-full">
                @else
                <img id="preview_photo" src={{ asset('images/badger.png') }} class="w-full">
                @endif
                <div class="mt-5">
                    <div>
                        {{-- <div @click="$refs.choose_photo.click()">Upload Image</div> --}}
                        <input type="file" name="photo" value="photo" onchange="previewImage(this.files[0], 'preview_photo')">
                    </div>
                </div>

                <div class="mt-3">
                    @if (session()->has('save_photo_logs'))
                    <div class="font-semibold w-full px-3 py-2 rounded bg-emerald-200 text-emerald-600 opacity-70">{{ session('save_photo_logs') }}</div>
                    @endif
                    @if (session()->has('delete_photo_logs'))
                    <div class="font-semibold w-full px-3 py-2 rounded bg-red-200 text-red-600 opacity-70">{{ session('delete_photo_logs') }}</div>
                    @endif
                </div>
                <div class="mt-3">
                    <button class="rounded px-3 py-2 text-xs font-semibold bg-blue-500 text-white hover:bg-blue-600 active:bg-blue-700 focus:ring focus:ring-blue-300" type="submit">
                        @if ($default_photo !== null)
                        <span>Update Photo</span>
                        @else
                        <span>Save Photo</span>
                        @endif
                    </button>
                </div>
            </div>
        </form>
        @if ($default_photo !== null)
        <form action="{{ route('produks.delete_photo', [$produk->id, $produk_photo_default->id, $default_photo->id]) }}" onsubmit="return confirm('Yakin ingin menghapus foto ini?')" method="POST">
            @csrf
            <button type="submit" class="px-2 py-1 font-semibold text-xs bg-pink-500 text-white hover:bg-pink-600 active:bg-pink-700 focus:ring focus:ring-pink-300 rounded mt-1">Delete</button>
        </form>
        @endif
    </div>

    @foreach ($subsidiary_photos as $key => $sub_photo)
    <div id="close_layer-{{ $key }}" class="absolute -top-28 right-0 -bottom-28 left-0 bg-slate-700 opacity-80 hidden" onclick="closeViewImage('sub_image-{{ $key }}', 'close_layer-{{ $key }}')"></div>
    <div id="sub_image-{{ $key }}" class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 hidden">
        <form action="{{ route('produks.update_photo', [$produk->id, $sub_photo->id]) }}" method="POST" class="mt-3 text-center" enctype="multipart/form-data">
            @csrf
            <div class="w-screen h-auto md:w-5/6 md:h-5/6 bg-white p-3 rounded-lg">
                <img id="preview_photo-{{ $key }}" src={{ asset("storage/$sub_photo->path") }} class="w-full">
                <div class="mt-5">
                    <div>
                        {{-- <div @click="$refs.choose_photo.click()">Upload Image</div> --}}
                        <input type="file" name="photo" value="photo" onchange="previewImage(this.files[0], 'preview_photo-{{ $key }}')">
                    </div>
                </div>

                <div class="mt-3">
                    @if (session()->has('save_photo_logs'))
                    <div class="font-semibold w-full px-3 py-2 rounded bg-emerald-200 text-emerald-600 opacity-70">{{ session('save_photo_logs') }}</div>
                    @endif
                    @if (session()->has('delete_photo_logs'))
                    <div class="font-semibold w-full px-3 py-2 rounded bg-red-200 text-red-600 opacity-70">{{ session('delete_photo_logs') }}</div>
                    @endif
                </div>
                <div class="mt-3">
                    <button class="rounded px-3 py-2 text-xs font-semibold bg-blue-500 text-white hover:bg-blue-600 active:bg-blue-700 focus:ring focus:ring-blue-300" type="submit">
                        <span>Update Photo</span>
                    </button>
                </div>
            </div>
        </form>
        <form action="{{ route('produks.delete_photo', [$produk->id, $produk_photo_subsidiary[$key]->id, $sub_photo->id]) }}" onsubmit="return confirm('Yakin ingin menghapus foto ini?')" method="POST">
            @csrf
            <button type="submit" class="px-2 py-1 font-semibold text-xs bg-pink-500 text-white hover:bg-pink-600 active:bg-pink-700 focus:ring focus:ring-pink-300 rounded mt-1">Delete</button>
        </form>
    </div>
    @endforeach
    {{-- END - VIEW IMAGE/PHOTO --}}


</main>

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
