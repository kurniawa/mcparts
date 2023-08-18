@extends('layouts.main')
@section('content')
{{-- <header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <h1 class="text-3xl font-bold tracking-tight text-gray-900">App</h1>
    </div>
  </header> --}}
<main>
    <x-errors-any></x-errors-any>
    <x-validation-feedback></x-validation-feedback>
    {{-- SEARCH / FILTER --}}
    <div class="relative rounded mt-9">
        <div class="flex absolute -top-6 left-1/2 -translate-x-1/2 z-20">
            @foreach ($pembelian_menus as $key_pembelian_menu => $pembelian_menu)
            @if ($route_now === $pembelian_menu['route'])
            @if ($key_pembelian_menu !== 0)
            <div class="border rounded-t-lg bg-white px-1 border-b-4 font-bold ml-2">{{ $pembelian_menu['name'] }}</div>
            @else
            <div class="border rounded-t-lg bg-white px-1 border-b-4 font-bold">{{ $pembelian_menu['name'] }}</div>
            @endif
            @else
            @if ($key_pembelian_menu !== 0)
            <a href="{{ route($pembelian_menu['route']) }}" class="border rounded-t-lg bg-white px-1 hover:bg-slate-100 ml-2">{{ $pembelian_menu['name'] }}</a>
            @else
            <a href="{{ route($pembelian_menu['route']) }}" class="border rounded-t-lg bg-white px-1 hover:bg-slate-100">{{ $pembelian_menu['name'] }}</a>
            @endif
            @endif
            @endforeach
        </div>
        <div class="relative bg-white border-t z-10">
            <div class="mx-1 py-1 sm:px-6 lg:px-8 text-xs mt-1">
                <div class="flex">
                    <button id="filter" class="border rounded border-yellow-500 text-yellow-500 px-3 py-1" onclick="toggle_light(this.id,'filter-content',[],['bg-yellow-200'], 'block')">Filter</button>
                    <button type="submit" class="border rounded border-emerald-300 text-emerald-500 font-semibold px-3 py-1 ml-1" id="btn_new_supplier" onclick="toggle_light(this.id, 'form_new_supplier', [], ['bg-emerald-200'], 'block')">+ Supplier</button>
                </div>
                <div class="hidden" id="filter-content">
                    <div class="rounded p-2 bg-white shadow drop-shadow inline-block mt-1">
                        <form action="" method="GET">
                            <div class="flex items-end">
                                <div>
                                    <div>Supplier :</div>
                                    <div class="mt-1">
                                        <input type="text" class="border rounded text-xs p-1" name="supplier_nama" placeholder="Nama Supplier..." id="supplier_nama">
                                        <input type="hidden" name="supplier_id" id="supplier_id">
                                    </div>
                                </div>
                                <div class="ml-2">
                                    <button type="submit" class="flex items-center bg-orange-500 text-white py-1 px-3 rounded hover:bg-orange-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                        </svg>
                                        <span class="ml-1">Search</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            {{-- END - SEARCH / FILTER --}}
            {{-- FORM_NEW_SUPPLIER --}}
            <div id="form_new_supplier" class="hidden">
                <div class="flex justify-center">
                    <form action="{{ route('suppliers.store') }}" method="POST" class="border rounded border-emerald-300 p-1 mt-1 lg:w-3/5 md:w-3/4">
                        @csrf
                        <div class="grid grid-cols-2">
                            {{-- $table->string("bentuk", 10)->nullable(); // PT, CV, Yayasan, Sekolah, dll.
                            $table->string("nama", 100);
                            $table->string("nama_pemilik", 100)->nullable();
                            $table->string("initial", 10)->nullable();
                            $table->string("keterangan")->nullable();
                            $table->string('creator', 50)->nullable();
                            $table->string('updater', 50)->nullable(); --}}
                            <table class="text-xs">
                                <tr>
                                    <td>Bentuk (*)</td><td>:</td>
                                    <td>
                                        <select name="bentuk" id="bentuk" class="rounded py-0">
                                            @foreach ($bentuks as $bentuk)
                                            <option value="{{ $bentuk }}">{{ $bentuk }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr><td>Nama (*)</td><td>:</td><td><input type="text" class="rounded p-1 text-xs" name="nama"></td></tr>
                                <tr><td>Nama Pemilik</td><td>:</td><td><input type="text" class="rounded p-1 text-xs" name="nama_pemilik"></td></tr>
                                <tr>
                                    <td>Initial (max. 5 chars)</td><td>:</td><td><input type="text" class="rounded p-1 text-xs" name="initial"></td>
                                </tr>
                                <tr>
                                    <td class="align-top">Keterangan (opt.)</td><td class="align-top">:</td><td><textarea name="keterangan" id="" cols="30" rows="5" class="text-xs rounded"></textarea></td>
                                </tr>
                            </table>
                            {{-- KONTAK --}}
                            <div>
                                <div class="flex justify-center mt-1">
                                    <div class="flex items-center bg-white rounded p-1 shadow drop-shadow">
                                        <h5 class="font-semibold ml-2 text-xs">Kontak:</h5>
                                    </div>
                                </div>
                                <table class="mt-2 text-xs">
                                    <tr>
                                        <td>Tipe</td><td>:</td>
                                        <td>
                                            <select class="border rounded py-0 ml-1" name="tipe">
                                                <option value="">-</option>
                                                @foreach ($tipe_kontaks as $tipe_kontak)
                                                <option value="{{ $tipe_kontak }}">{{ $tipe_kontak }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                    <tr><td>Kode Area</td><td>:</td><td><input type="text" name="kodearea" class="p-1 text-xs rounded ml-1 w-1/2"></td></tr>
                                    <tr><td>Nomor</td><td>:</td><td><input type="text" name="nomor" class="p-1 text-xs rounded ml-1"></td></tr>
                                </table>
                            </div>
                            {{-- END - KONTAK --}}
                        </div>
                        <div class="flex justify-center mt-2 text-xs">
                            <div class="flex items-center bg-white rounded p-1 shadow drop-shadow">
                                <h5 class="font-semibold ml-2">Alamat:</h5>
                            </div>
                        </div>
                        <table class="mt-1 w-full">
                            <tr><td>jalan</td><td>:</td><td><input type="text" name="jalan" class="text-xs p-1 rounded"></td></tr>
                            <tr><td>komplek</td><td>:</td><td><input type="text" name="komplek" class="text-xs p-1 rounded"></td></tr>
                            <tr>
                                <td>rt</td><td>:</td><td><input type="text" name="rt" class="text-xs p-1 rounded"></td>
                                <td>rw</td><td>:</td><td><input type="text" name="rw" class="text-xs p-1 rounded"></td>
                            </tr>
                            <tr>
                                <td>desa</td><td>:</td><td><input type="text" name="desa" class="text-xs p-1 rounded"></td>
                                <td>kelurahan</td><td>:</td><td><input type="text" name="kelurahan" class="text-xs p-1 rounded">
                            </tr>
                            <tr>
                                <td>kecamatan</td><td>:</td><td><input type="text" name="kecamatan" class="text-xs p-1 rounded"></td>
                                <td>kota</td><td>:</td><td><input type="text" name="kota" class="text-xs p-1 rounded">
                            </tr>
                            <tr><td>kodepos</td><td>:</td><td><input type="text" name="kodepos" class="text-xs p-1 rounded"></td></tr>
                            <tr>
                                <td>kabupaten</td><td>:</td><td><input type="text" name="kabupaten" class="text-xs p-1 rounded"></td>
                                <td>provinsi</td><td>:</td><td><input type="text" name="provinsi" class="text-xs p-1 rounded"></td>
                            </tr>
                            <tr>
                                <td>pulau</td><td>:</td><td><input type="text" name="pulau" class="text-xs p-1 rounded"></td>
                                <td>negara</td><td>:</td><td><input type="text" name="negara" class="text-xs p-1 rounded"></td>
                            </tr>
                            <tr>
                                <td>(*)short(daerah)</td><td>:</td><td><input type="text" name="short" class="text-xs p-1 rounded"></td>
                                <td>(*)long</td><td>:</td><td><textarea name="long" id="" cols="30" rows="4" class="border border-slate-400 rounded p-1 text-xs"></textarea></td>
                            </tr>
                        </table>

                        <div class="text-center mt-2">
                            <button type="submit" class="border-2 border-emerald-300 bg-emerald-200 text-emerald-600 rounded-lg font-semibold py-1 px-3 hover:bg-emerald-300">+ Supplier Baru</button>
                        </div>
                    </form>
                </div>
            </div>
            {{-- END - FORM_NEW_SUPPLIER --}}
            <div class="flex justify-center">
                <div class='pb-1 text-xs lg:w-1/2 md:w-3/4'>
                    <table class="table-nice w-full">
                        @for ($i = 0; $i < count($suppliers); $i++)
                        <tr>
                            <td>
                                <div class="rounded-full bg-violet-200 w-7 h-7 flex justify-center items-center">
                                    {{ $suppliers[$i]['initial'] }}
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('suppliers.show', $suppliers[$i]->id) }}" class="text-sky-500">
                                    @if ($alamats[$i] !== null)
                                    {{ $suppliers[$i]['nama'] }} - {{ $alamats[$i]['short'] }}
                                    @else
                                    {{ $suppliers[$i]['nama'] }}
                                    @endif
                                </a>
                            </td>
                            <td>
                                <div class="flex items-center justify-end">
                                    <button id="btn_dd_supplier-{{ $i }}" class="border rounded" onclick="showDropdown(this.id, 'dd_supplier-{{ $i }}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr class="hidden" id="dd_supplier-{{ $i }}">
                            <td colspan="3">
                                <div class="flex justify-between items-center">
                                    <table>
                                        <tr>
                                            <td>
                                                @if ($alamats[$i]!==null)
                                                @if ($alamats[$i]['long']!==null)
                                                @foreach (json_decode($alamats[$i]['long'],true) as $alamat)
                                                {{ $alamat }}<br>
                                                @endforeach
                                                @endif
                                                @else
                                                -
                                                @endif
                                            </td>
                                            <td>
                                                @if ($supplier_kontaks[$i]!==null)
                                                @if ($supplier_kontaks[$i]['kodearea']!==null)
                                                {{ $supplier_kontaks[$i]['kodearea'] }} {{ $supplier_kontaks[$i]['nomor'] }}
                                                @else
                                                {{ $supplier_kontaks[$i]['nomor'] }}
                                                @endif
                                                @else
                                                -
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                    <div>
                                        <form action="{{ route('suppliers.delete', $suppliers[$i]->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus supplier ini?')">
                                            @csrf
                                            <button class="bg-red-200 text-red-500 rounded">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endfor
                    </table>
                </div>
            </div>
            <div class="w-56"></div>
        </div>
    </div>
</main>

<script>
    const label_supplier = {!! json_encode($label_supplier, JSON_HEX_TAG) !!}

    $("#supplier_nama").autocomplete({
        source: label_supplier,
        select: function(event, ui) {
            $("#supplier_id").val(ui.item.id);
        }
    });


</script>

@endsection
