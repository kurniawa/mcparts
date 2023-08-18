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
    <div class="flex justify-center">
        @foreach ($spk_menus as $key_spk_menu => $spk_menu)
        @if ($route_now === $spk_menu['route'])
        @if ($key_spk_menu !== 0)
        <div class="border rounded-t-lg bg-white px-1 border-b-4 font-bold ml-2">{{ $spk_menu['name'] }}</div>
        @else
        <div class="border rounded-t-lg bg-white px-1 border-b-4 font-bold">{{ $spk_menu['name'] }}</div>
        @endif
        @else
        @if ($key_spk_menu !== 0)
        <a href="{{ route($spk_menu['route']) }}" class="border rounded-t-lg bg-white px-1 hover:bg-slate-100 ml-2">{{ $spk_menu['name'] }}</a>
        @else
        <a href="{{ route($spk_menu['route']) }}" class="border rounded-t-lg bg-white px-1 hover:bg-slate-100">{{ $spk_menu['name'] }}</a>
        @endif
        @endif
        @endforeach
    </div>
    {{-- SEARCH / FILTER --}}
    <div class="mx-1 py-1 sm:px-6 lg:px-8 text-xs">
        <div class="flex">
            <button id="filter" class="border rounded border-yellow-500 text-yellow-500 px-3 py-1" onclick="toggle_light(this.id,'filter-content',[],['bg-yellow-200'], 'block')">Filter</button>
            <button type="submit" class="border rounded border-emerald-300 text-emerald-500 font-semibold px-3 py-1 ml-1" id="btn_new_ekspedisi" onclick="toggle_light(this.id, 'form_new_ekspedisi', [], ['bg-emerald-200'], 'block')">+ Ekspedisi</button>
        </div>
        {{-- FORM NEW EKSPEDISI --}}
        <div id="form_new_ekspedisi" class="hidden">
            <div class="flex justify-center">
                <form action="{{ route('ekspedisis.store') }}" method="POST" class="border rounded border-emerald-300 p-1 mt-1 lg:w-1/2 md:w-3/4">
                    @csrf
                    <div class="grid grid-cols-2">
                        <table>
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
                            <tr>
                                <td class="align-top">Keterangan (opt.)</td><td class="align-top">:</td><td><textarea name="keterangan" id="" cols="30" rows="5" class="text-xs rounded"></textarea></td>
                            </tr>
                        </table>
                        {{-- KONTAK --}}
                        <div>
                            <div class="flex justify-center mt-1">
                                <div class="flex items-center bg-white rounded p-1 shadow drop-shadow">
                                    <h5 class="font-semibold ml-2">Kontak:</h5>
                                </div>
                            </div>
                            <table class="mt-2">
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
                    <div class="flex justify-center mt-2">
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
                        <button type="submit" class="bg-emerald-500 rounded text-white py-2 px-5 font-semibold">Tambah Ekspedisi Baru</button>
                    </div>
                </form>
            </div>
        </div>
        {{-- END - FORM NEW EKSPEDISI --}}
        <div class="hidden" id="filter-content">
            <div class="rounded p-2 bg-white shadow drop-shadow inline-block mt-1">
                <form action="" method="GET">
                    <div class="ml-1 mt-2 flex items-center">
                        <div class="flex mt-1">
                            <input type="text" class="input" name="ekspedisi_nama" placeholder="Nama Customer..." id="ekspedisi_nama">
                            {{-- <input type="hidden" name="pelanggan_id" id="pelanggan_id"> --}}
                        </div>
                        <div>
                            <button type="submit" class="ml-2 flex items-center bg-yellow-500 text-white py-1 px-1 rounded hover:bg-yellow-700">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
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
    <div class="flex justify-center">
        <div class='pb-1 text-xs lg:w-1/2 md:w-3/4'>
            <table class="table-nice w-full">
                @for ($i = 0; $i < count($ekspedisis); $i++)
                <tr class="border-b">
                    <td>
                        <a href="{{ route('ekspedisis.show', $ekspedisis[$i]->id) }}" class="text-sky-500">
                            @if ($alamats[$i] !== null)
                            {{ $ekspedisis[$i]['nama'] }} - {{ $alamats[$i]['short'] }}
                            @else
                            {{ $ekspedisis[$i]['nama'] }}
                            @endif
                        </a>
                    </td>
                    <td>
                        <button id="btn_detail_pelanggan-{{ $i }}" class="border rounded" onclick="showDropdown(this.id, 'detail_pelanggan-{{ $i }}')">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>
                    </td>
                </tr>
                <tr class="hidden" id="detail_pelanggan-{{ $i }}">
                    <td colspan="3">
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
                                    @if ($ekspedisi_kontaks[$i]!==null)
                                    @if ($ekspedisi_kontaks[$i]['kodearea']!==null)
                                    {{ $ekspedisi_kontaks[$i]['kodearea'] }} {{ $ekspedisi_kontaks[$i]['nomor'] }}
                                    @else
                                    {{ $ekspedisi_kontaks[$i]['nomor'] }}
                                    @endif
                                    @else
                                    -
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                @endfor
            </table>
        </div>
    </div>
</main>


@endsection
