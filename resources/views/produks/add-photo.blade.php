@extends('layouts.main')
@section('content')
<header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <h1 class="text-xl font-bold tracking-tight text-gray-900">Add Photo</h1>
      <div class="flex gap-2 items-center">
        <span>Untuk:</span><span class="font-bold text-lg text-gray-600">{{ $produk->nama }}</span>
      </div>
    </div>
  </header>
<main class="mb-9 relative">
    <x-errors-any></x-errors-any>
    <x-validation-feedback></x-validation-feedback>

    {{-- DAFTAR FOTO --}}
    <div class="flex mx-2">
        <div class="flex mt-2 border p-1 rounded gap-1">
            @if (count($produk_photos) > 0)
            @foreach ($produk_photos as $key => $produk_photo)
            <button type="button" class="rounded-lg overflow-hidden w-1/5 md:w-28" onclick="showViewImage('view_photo-{{ $key }}', 'close_layer-{{ $key }}')">
                <img src="{{ asset("storage/" . $photos[$key]->path) }}" alt="" class="w-full">
            </button>
            @endforeach
            @endif
        </div>
    </div>
    {{-- END - DAFTAR FOTO --}}

    {{-- PILIH FOTO DARI PRODUK LAIN --}}
    <div class="bg-white rounded p-2 shadow drop-shadow mt-3 mx-2">
        <h2 class="font-bold text-lg">Tambah Foto dari Produk Lain</h2>
        <div class="flex gap-2 items-center">
            <input id="cari_nama_produk" type="text" name="" id="" class="p-3 rounded border-slate-300" placeholder="cari produk...">
            {{-- <div>
                <button type="button" class="bg-orange-500 rounded-lg p-2 text-white w-28 flex gap-1 items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <span>Cari</span>
                </button>
            </div> --}}
        </div>

        <form id="photos_search_results" action="{{ route('produk_photos.add_photo_from_other_product', $produk->id) }}" class="mt-2 border p-1 rounded" method="POST"></form>
    </div>
    {{-- END - PILIH FOTO DARI PRODUK LAIN --}}

    {{-- TAMBAH PHOTO BARU --}}
    <div class="flex justify-center mt-3">
        <div class="border rounded-lg border-rose-300 p-2">
            <div class="text-center">
                <h3 class="text-lg font-bold">Atau<br />Tambah Foto Baru</h3>
            </div>
            <form action="{{ route('produk_photos.store_photo', $produk->id) }}" method="POST" class="mt-2" enctype="multipart/form-data">
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
                    <button type="submit" class="rounded p-2 bg-blue-500 font-semibold text-white">Tambah Foto Baru</button>
                </div>
            </form>
        </div>
    </div>
    {{-- END - TAMBAH PHOTO BARU --}}
</main>

<script>
    const all_product_photos = {!! json_encode($all_product_photos, JSON_HEX_TAG) !!};
    console.log(all_product_photos);

    $('#cari_nama_produk').autocomplete({
        source: all_product_photos,
        select: function (event, ui) {
            console.log(ui.item);
            document.getElementById('cari_nama_produk').value = ui.item.value;

            let html_search_results = '<div class="flex gap-3">';

            if (ui.item.photos_data.length !== 0) {
                ui.item.photos_data.forEach((data, index) => {
                    html_search_results += `
                        <div class="flex gap-1 items-center">
                            <input id="photo_id-${index}" type="checkbox" name="photo_id[]" value="${data.id}">
                            <label for="photo_id-${index}" class="rounded-lg overflow-hidden w-1/5 md:w-28">
                                <img src="{{ url('') }}/storage/${data.path}">
                            </label>
                        </div>
                    `;
                });
            }

            html_search_results += `
                </div>
                @csrf
                <div class="mt-2 flex justify-center">
                    <button type="submit" class="rounded-lg bg-blue-500 text-white p-2 text-xs">Tambah Foto Dari Produk Lain</button>
                </div>
            `;

            document.getElementById('photos_search_results').innerHTML = html_search_results;
            // document.getElementById('nama_pelanggan').value = ui.item.value;
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
