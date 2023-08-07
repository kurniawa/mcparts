@extends('layouts.main')
@section('content')
<header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <h1 class="text-3xl font-bold tracking-tight text-gray-900">Data Pelanggan: {{ $pelanggan->nama }}</h1>
    </div>
  </header>
<main>
    <x-errors-any></x-errors-any>
    <x-validation-feedback></x-validation-feedback>
    <div class="flex justify-center text-xs mb-9">
        <div class="lg:w-1/2 md:w-3/4 border rounded p-1 bg-white shadow drop-shadow-sm">
            {{-- ALAMAT --}}
            <div class="flex items-center">
                <div class="flex items-center bg-white rounded shadow drop-shadow p-1">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                    </svg>
                    <h5 class="font-semibold ml-2">Daftar Alamat:</h5>
                </div>
                <form action="{{ route('pelanggans.alamat_add', $pelanggan['id']) }}" method="GET" class="m-0 ml-1">
                    <button type="submit" class="rounded bg-emerald-500 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </button>
                </form>
            </div>
            <div class="flex mt-1">
                @foreach ($alamats as $key_alamat => $alamat)
                @if ($pelanggan_alamats[$key_alamat]->tipe === 'UTAMA')
                <div class="p-1 border-2 rounded relative border-emerald-300 @if($key_alamat !== 0) ml-3 @endif">
                @else
                <div class="p-1 border rounded @if($key_alamat !== 0) ml-3 @endif">
                @endif
                    @if ($pelanggan_alamats[$key_alamat]->tipe === 'UTAMA')
                    <div class="text-emerald-500 absolute -bottom-3 -right-3">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                            <path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0112 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 013.498 1.307 4.491 4.491 0 011.307 3.497A4.49 4.49 0 0121.75 12a4.49 4.49 0 01-1.549 3.397 4.491 4.491 0 01-1.307 3.497 4.491 4.491 0 01-3.497 1.307A4.49 4.49 0 0112 21.75a4.49 4.49 0 01-3.397-1.549 4.49 4.49 0 01-3.498-1.306 4.491 4.491 0 01-1.307-3.498A4.49 4.49 0 012.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 011.307-3.497 4.49 4.49 0 013.497-1.307zm7.007 6.387a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    @endif
                    @foreach (json_decode($alamat['long'], true) as $long)
                    <span class="block">{{ $long }}</span>
                    @endforeach
                    <span class="block text-slate-400 font-semibold">{{ $alamat['short'] }}</span>
                    <div class="flex justify-end items-center">
                        @if ($pelanggan_alamats[$key_alamat]->tipe !== 'UTAMA')
                        <form action="{{ route('pelanggans.alamat_utama', [$pelanggan->id, $alamat->id]) }}" method="POST" class="m-0 flex items-center mr-1" onsubmit="return confirm('Jadikan sebagai alamat UTAMA?')">
                            @csrf
                            <button type="submit" class="text-slate-300">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                    <path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0112 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 013.498 1.307 4.491 4.491 0 011.307 3.497A4.49 4.49 0 0121.75 12a4.49 4.49 0 01-1.549 3.397 4.491 4.491 0 01-1.307 3.497 4.491 4.491 0 01-3.497 1.307A4.49 4.49 0 0112 21.75a4.49 4.49 0 01-3.397-1.549 4.49 4.49 0 01-3.498-1.306 4.491 4.491 0 01-1.307-3.498A4.49 4.49 0 012.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 011.307-3.497 4.49 4.49 0 013.497-1.307zm7.007 6.387a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </form>
                        @endif
                        <a href="{{ route('pelanggans.alamat_edit', [$pelanggan['id'], $alamat['id']]) }}" class="bg-slate-400 rounded text-white p-1">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                                <path d="M21.731 2.269a2.625 2.625 0 00-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 000-3.712zM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 00-1.32 2.214l-.8 2.685a.75.75 0 00.933.933l2.685-.8a5.25 5.25 0 002.214-1.32l8.4-8.4z" />
                                <path d="M5.25 5.25a3 3 0 00-3 3v10.5a3 3 0 003 3h10.5a3 3 0 003-3V13.5a.75.75 0 00-1.5 0v5.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5V8.25a1.5 1.5 0 011.5-1.5h5.25a.75.75 0 000-1.5H5.25z" />
                            </svg>
                        </a>
                        <form action="{{ route('pelanggans.delete_alamat', $alamat->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus alamat ini?')" class="m-0 ml-1 flex items-center">
                            @csrf
                            <button type="submit" class="bg-red-500 text-white rounded p-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            {{-- END - ALAMAT --}}

            {{-- KONTAK --}}
            <div class="flex items-center mt-2">
                <div class="flex items-center bg-white rounded p-1 shadow drop-shadow">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                    </svg>
                    <h5 class="font-semibold ml-2">Daftar Kontak:</h5>
                </div>
                <button id="btn_tambah_kontak" type="button" class="rounded border border-sky-300 text-sky-500 ml-1" onclick="toggle_light(this.id, 'form_tambah_kontak', [], ['bg-sky-200'], 'block')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </button>
            </div>
            {{-- TAMBAH_KONTAK --}}
            <div class="hidden" id="form_tambah_kontak">
                <div class="inline-block border rounded p-2 mt-1">
                    <form action="{{ route('pelanggans.kontak_add', $pelanggan->id) }}" class="flex items-center" method="POST">
                        @csrf
                        <span>Tipe:</span>
                        <select class="border rounded py-0 ml-1" name="tipe">
                            <option value="">-</option>
                            <option value="seluler">seluler</option>
                            <option value="kantor">kantor</option>
                            <option value="rumah">rumah</option>
                        </select>
                        <span class="ml-2">Kodearea:</span>
                        <input type="text" name="kodearea" class="p-1 text-xs rounded ml-1 w-1/6">
                        <span class="ml-2">Nomor:</span>
                        <input type="text" name="nomor" class="p-1 text-xs rounded ml-1">
                        <button type="submit" class="bg-sky-500 text-white p-1 rounded ml-2">Tambah Kontak</button>
                    </form>
                </div>
            </div>
            {{-- END - TAMBAH_KONTAK --}}
            {{-- KONTAK TERSEDIA --}}
            <div class="flex mt-1">
                @foreach ($pelanggan_kontaks as $key_kontak => $kontak)
                @if ($kontak->is_aktual === 'yes')
                <div class="p-1 border-2 rounded relative border-sky-300 @if($key_kontak !== 0) ml-3 @endif">
                @else
                <div class="p-1 border rounded @if($key_kontak !== 0) ml-3 @endif">
                @endif
                    @if ($kontak->is_aktual === 'yes')
                    <div class="text-sky-500 absolute -bottom-3 -right-3">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                            <path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0112 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 013.498 1.307 4.491 4.491 0 011.307 3.497A4.49 4.49 0 0121.75 12a4.49 4.49 0 01-1.549 3.397 4.491 4.491 0 01-1.307 3.497 4.491 4.491 0 01-3.497 1.307A4.49 4.49 0 0112 21.75a4.49 4.49 0 01-3.397-1.549 4.49 4.49 0 01-3.498-1.306 4.491 4.491 0 01-1.307-3.498A4.49 4.49 0 012.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 011.307-3.497 4.49 4.49 0 013.497-1.307zm7.007 6.387a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    @endif
                    <span class="block">
                        @if ($kontak['kodearea'] !== null)
                        {{ $kontak['kodearea'] }} - {{ $kontak['nomor'] }}
                        @else
                        {{ $kontak['nomor'] }}
                        @endif
                    </span>
                    <div class="flex justify-end items-center">
                        @if ($kontak->is_aktual !== 'yes')
                        <form action="{{ route('pelanggans.kontak_utama', [$pelanggan->id, $pelanggan_kontaks[$key_kontak]->id]) }}" method="POST" class="m-0 flex items-center mr-1" onsubmit="return confirm('Jadikan sebagai kontak UTAMA?')">
                            @csrf
                            <button type="submit" class="text-slate-300">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                    <path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0112 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 013.498 1.307 4.491 4.491 0 011.307 3.497A4.49 4.49 0 0121.75 12a4.49 4.49 0 01-1.549 3.397 4.491 4.491 0 01-1.307 3.497 4.491 4.491 0 01-3.497 1.307A4.49 4.49 0 0112 21.75a4.49 4.49 0 01-3.397-1.549 4.49 4.49 0 01-3.498-1.306 4.491 4.491 0 01-1.307-3.498A4.49 4.49 0 012.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 011.307-3.497 4.49 4.49 0 013.497-1.307zm7.007 6.387a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </form>
                        @endif
                        {{-- <button class="bg-slate-400 rounded text-white p-1"></button> --}}
                        <button class="border border-slate-300 rounded text-slate-400" id="btn_kontak_edit-{{ $key_kontak }}" onclick="toggle_light(this.id, 'form_kontak_edit-{{ $key_kontak }}', [], ['bg-slate-200'], 'block')">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                                <path d="M21.731 2.269a2.625 2.625 0 00-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 000-3.712zM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 00-1.32 2.214l-.8 2.685a.75.75 0 00.933.933l2.685-.8a5.25 5.25 0 002.214-1.32l8.4-8.4z" />
                                <path d="M5.25 5.25a3 3 0 00-3 3v10.5a3 3 0 003 3h10.5a3 3 0 003-3V13.5a.75.75 0 00-1.5 0v5.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5V8.25a1.5 1.5 0 011.5-1.5h5.25a.75.75 0 000-1.5H5.25z" />
                            </svg>
                        </button>
                        <form action="{{ route('pelanggans.kontak_delete', $pelanggan_kontaks[$key_kontak]->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kontak ini?')" class="m-0">
                            @csrf
                            <button type="submit" class="bg-red-500 text-white rounded p-1 ml-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>
                        </form>
                    </div>
                    {{-- EDIT_KONTAK --}}
                    <form action="{{ route('pelanggans.kontak_edit', $pelanggan_kontaks[$key_kontak]->id) }}" method="POST" class="mt-1 border rounded p-1 hidden" id="form_kontak_edit-{{ $key_kontak }}">
                        @csrf
                        <table>
                            <tr>
                                <td>Tipe</td><td>:</td>
                                <td>
                                    <select class="border rounded py-0 ml-1" name="tipe">
                                        <option value="{{ $kontak->tipe }}">{{ $kontak->tipe }}</option>
                                        <option value="">-</option>
                                        <option value="seluler">seluler</option>
                                        <option value="kantor">kantor</option>
                                        <option value="rumah">rumah</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Kode Area</td><td>:</td><td><input type="text" name="kodearea" class="ml-1 p-1 rounded text-xs w-16" value="{{ $kontak->kodearea }}"></td>
                            </tr>
                            <tr>
                                <td>Nomor</td><td>:</td><td><input type="text" name="nomor" class="ml-1 p-1 rounded text-xs" value="{{ $kontak->nomor }}"></td>
                            </tr>
                        </table>
                        <div class="text-end mt-1">
                            <button type="submit" class="bg-sky-500 text-white rounded p-1">Konfirmasi Edit</button>
                        </div>
                    </form>
                    {{-- END - EDIT_KONTAK --}}
                </div>
                @endforeach
            </div>
            {{-- END - KONTAK TERSEDIA --}}
            {{-- END -KONTAK --}}

            {{-- EKSPEDISI --}}
            <div class="mt-9">
                <div class="flex items-center">
                    <div class="flex items-center bg-white rounded shadow drop-shadow p-1">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                        </svg>
                        <h5 class="font-semibold ml-2">Daftar Ekspedisi:</h5>
                    </div>
                    <button type="button" class="border border-orange-300 rounded text-orange-500 ml-1" id="btn_ekspedisi_add" onclick="toggle_light(this.id, 'form_ekspedisi_add', [], ['bg-orange-200'], 'block')">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </button>
                </div>
                <div id="form_ekspedisi_add" class="hidden">
                    <div class="flex items-center">
                        <form action="{{ route('pelanggans.ekspedisi_add', $pelanggan['id']) }}" method="POST" id="form_ekspedisi_add" class="border rounded p-1 mt-1 bg-white shadow drop-shadow-sm">
                            @csrf
                            <div class="flex items-center">
                                <span>Nama Ekspedisi:</span>
                                <input type="text" name="ekspedisi_nama" id="ekspedisi_nama" class="rounded text-xs p-1 ml-1">
                                <button type="submit" class="px-1 rounded bg-emerald-300 text-emerald-500 ml-2" name="ekspedisi_id" id="ekspedisi_id">confirm</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="flex mt-1">
                @foreach ($ekspedisis as $key_ekspedisi => $ekspedisi)
                @if ($pelanggan_ekspedisis[$key_ekspedisi]->tipe === 'UTAMA')
                <div class="p-1 border-2 rounded relative border-orange-300 @if($key_ekspedisi !== 0) ml-3 @endif">
                @else
                <div class="p-1 border rounded @if($key_ekspedisi !== 0) ml-3 @endif">
                @endif
                    @if ($pelanggan_ekspedisis[$key_ekspedisi]->tipe === 'UTAMA')
                    <div class="text-orange-500 absolute -bottom-3 -right-3">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                            <path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0112 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 013.498 1.307 4.491 4.491 0 011.307 3.497A4.49 4.49 0 0121.75 12a4.49 4.49 0 01-1.549 3.397 4.491 4.491 0 01-1.307 3.497 4.491 4.491 0 01-3.497 1.307A4.49 4.49 0 0112 21.75a4.49 4.49 0 01-3.397-1.549 4.49 4.49 0 01-3.498-1.306 4.491 4.491 0 01-1.307-3.498A4.49 4.49 0 012.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 011.307-3.497 4.49 4.49 0 013.497-1.307zm7.007 6.387a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    @endif
                    <span class="block font-semibold">{{ $ekspedisi['nama'] }}</span>
                    @foreach (json_decode($alamat_ekspedisis[$key_ekspedisi]['long'], true) as $long)
                    <span class="block">{{ $long }}</span>
                    @endforeach
                    <span class="block text-slate-400 font-semibold">{{ $alamat_ekspedisis[$key_ekspedisi]['short'] }}</span>
                    <div class="flex justify-end items-center">
                        @if ($pelanggan_ekspedisis[$key_ekspedisi]->tipe !== 'UTAMA')
                        <form action="{{ route('pelanggans.ekspedisi_utama', [$pelanggan->id, $pelanggan_ekspedisis[$key_ekspedisi]->id]) }}" method="POST" class="m-0 flex items-center mr-1" onsubmit="return confirm('Jadikan sebagai Ekspedisi UTAMA?')">
                            @csrf
                            <button type="submit" class="text-slate-300">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                    <path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0112 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 013.498 1.307 4.491 4.491 0 011.307 3.497A4.49 4.49 0 0121.75 12a4.49 4.49 0 01-1.549 3.397 4.491 4.491 0 01-1.307 3.497 4.491 4.491 0 01-3.497 1.307A4.49 4.49 0 0112 21.75a4.49 4.49 0 01-3.397-1.549 4.49 4.49 0 01-3.498-1.306 4.491 4.491 0 01-1.307-3.498A4.49 4.49 0 012.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 011.307-3.497 4.49 4.49 0 013.497-1.307zm7.007 6.387a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </form>
                        @endif
                        <form action="{{ route('pelanggans.ekspedisi_delete', $pelanggan_ekspedisis[$key_ekspedisi]->id) }}" method="POST" onsubmit="return confirm('Hapus relasi Pelanggan - Ekspedisi ini?')" class="m-0 ml-1 flex items-center">
                            @csrf
                            <button type="submit" class="bg-red-500 text-white rounded p-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            {{-- END - EKSPEDISI --}}

            {{-- TRANSIT --}}
            <div class="flex items-center mt-9">
                <div class="flex items-center bg-white rounded shadow drop-shadow p-1">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                    </svg>
                    <h5 class="font-semibold ml-2">Daftar Transit:</h5>
                </div>
                <button type="submit" class="rounded border border-violet-300 text-violet-500 ml-1" id="btn_transit_add" onclick="toggle_light(this.id, 'form_transit_add', [], ['bg-violet-200'], 'block')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </button>
            </div>
            <div id="form_transit_add" class="hidden">
                <div class="flex items-center">
                    <form action="{{ route('pelanggans.transit_add', $pelanggan['id']) }}" method="POST" class="border rounded p-1 mt-1 bg-white shadow drop-shadow-sm">
                        @csrf
                        <div class="flex items-center">
                            <span>Nama Transit:</span>
                            <input type="text" name="transit_nama" id="transit_nama" class="rounded text-xs p-1 ml-1">
                            <button type="submit" class="px-1 rounded bg-emerald-300 text-emerald-500 ml-2" name="transit_id" id="transit_id">confirm</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="flex mt-1">
                @foreach ($transits as $key_transit => $transit)
                @if ($pelanggan_transits[$key_transit]->tipe === 'UTAMA')
                <div class="p-1 border-2 rounded relative border-violet-300 @if($key_transit !== 0) ml-3 @endif">
                @else
                <div class="p-1 border rounded @if($key_transit !== 0) ml-3 @endif">
                @endif
                    @if ($pelanggan_transits[$key_transit]->tipe === 'UTAMA')
                    <div class="text-violet-500 absolute -bottom-3 -right-3">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                            <path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0112 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 013.498 1.307 4.491 4.491 0 011.307 3.497A4.49 4.49 0 0121.75 12a4.49 4.49 0 01-1.549 3.397 4.491 4.491 0 01-1.307 3.497 4.491 4.491 0 01-3.497 1.307A4.49 4.49 0 0112 21.75a4.49 4.49 0 01-3.397-1.549 4.49 4.49 0 01-3.498-1.306 4.491 4.491 0 01-1.307-3.498A4.49 4.49 0 012.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 011.307-3.497 4.49 4.49 0 013.497-1.307zm7.007 6.387a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    @endif
                    <span class="block font-semibold">{{ $transit['nama'] }}</span>
                    @foreach (json_decode($alamat_transits[$key_transit]['long'], true) as $long)
                    <span class="block">{{ $long }}</span>
                    @endforeach
                    <span class="block text-slate-400 font-semibold">{{ $alamat_transits[$key_transit]['short'] }}</span>
                    <div class="flex justify-end items-center">
                        @if ($pelanggan_transits[$key_transit]->tipe !== 'UTAMA')
                        <form action="{{ route('pelanggans.transit_utama', [$pelanggan->id, $pelanggan_transits[$key_transit]->id]) }}" method="POST" class="m-0 flex items-center mr-1" onsubmit="return confirm('Jadikan sebagai Transit UTAMA?')">
                            @csrf
                            <button type="submit" class="text-slate-300">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                    <path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0112 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 013.498 1.307 4.491 4.491 0 011.307 3.497A4.49 4.49 0 0121.75 12a4.49 4.49 0 01-1.549 3.397 4.491 4.491 0 01-1.307 3.497 4.491 4.491 0 01-3.497 1.307A4.49 4.49 0 0112 21.75a4.49 4.49 0 01-3.397-1.549 4.49 4.49 0 01-3.498-1.306 4.491 4.491 0 01-1.307-3.498A4.49 4.49 0 012.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 011.307-3.497 4.49 4.49 0 013.497-1.307zm7.007 6.387a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </form>
                        @endif
                        <form action="{{ route('pelanggans.transit_delete', $pelanggan_transits[$key_transit]->id) }}" method="POST" onsubmit="return confirm('Hapus relasi Pelanggan - Transit ini?')" class="m-0 ml-1 flex items-center">
                            @csrf
                            <button type="submit" class="bg-red-500 text-white rounded p-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            {{-- END - TRANSIT --}}
        </div>
    </div>
</main>

<script>
    const label_ekspedisis = {!! json_encode($label_ekspedisis, JSON_HEX_TAG) !!};
    setTimeout(() => {
        $('#ekspedisi_nama').autocomplete({
            source: label_ekspedisis,
            select: function (event, ui) {
                // console.log(ui.item);
                document.getElementById('ekspedisi_id').value = ui.item.id;
                document.getElementById('ekspedisi_nama').value = ui.item.value;
            }
        });

        $('#transit_nama').autocomplete({
            source: label_ekspedisis,
            select: function (event, ui) {
                // console.log(ui.item);
                document.getElementById('transit_id').value = ui.item.id;
                document.getElementById('transit_nama').value = ui.item.value;
            }
        });
    }, 1000);
</script>

@endsection
