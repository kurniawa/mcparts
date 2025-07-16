<div id="{{ isset($id) ? $id : '' }}" class="{{ isset($class) ? $class : '' }}">
    {{-- PHOTO DEFAULT --}}
    <div class="flex justify-center">
        @if ($defaultPhoto !== null)
        <div class="flex justify-center items-center md:w-96 md:h-96 rounded-lg overflow-hidden bg-white shadow drop-shadow p-2">
            <button type="button" class="rounded-lg overflow-hidden bg-orange-100" onclick="showViewImage('default_image', 'close_layer')">
                <img class="w-full" src="{{ asset("storage/$defaultPhoto->path") }}">
            </button>
        </div>
        @else
        <div class="w-72 h-72 md:w-96 md:h-96 rounded-lg overflow-hidden bg-white shadow drop-shadow p-2">
            <img class="object-cover" src="{{ asset('images/badger.png') }}" alt="Profile Picture">
        </div>
        @endif
    </div>
    {{-- END - PHOTO DEFAULT --}}

    {{-- PHOTO SUBSIDIARY --}}
    <div class="grid grid-cols-4 md:w-96 h-auto mt-2 border p-1 rounded gap-1">
        @if (count($subsidiaryPhotos) > 0)
        @foreach ($subsidiaryPhotos as $key => $sub_photo)
        <button type="button" class="rounded-lg overflow-hidden" onclick="showViewImage('sub_image-{{ $key }}', 'close_layer-{{ $key }}')">
            <img src="{{ asset("storage/$sub_photo->path") }}" alt="" class="w-full">
        </button>
        @endforeach
        @endif
    </div>
    {{-- END - PHOTO SUBSIDIARY --}}
    <div class="flex justify-center mt-3">
        <a href="{{ route('produk_photos.add_photo', $item->id) }}" class="p-2 bg-blue-500 font-semibold text-white rounded-xl">
            Penambahan Foto Baru
        </a>
    </div>

</div>