@extends('layouts.main')
@section('content')
<header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <h1 class="text-3xl font-bold tracking-tight text-gray-900">Tambah Pelanggan Baru</h1>
    </div>
  </header>
<main class="flex justify-center text-xs mt-2">
    <div class="lg:w-1/2 md:w-3/4 border rounded p-1 bg-white shadow drop-shadow-sm">
        <form class="rounded" action="{{ route('pelanggans.store') }}" method="POST">
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
                    <tr><td>Sapaan</td><td>:</td><td><input type="text" class="rounded p-1 text-xs" name="sapaan"></td></tr>
                    <tr><td>Gelar</td><td>:</td><td><input type="text" class="rounded p-1 text-xs" name="gelar"></td></tr>
                    <tr><td>NIK</td><td>:</td><td><input type="text" class="rounded p-1 text-xs" name="nik"></td></tr>
                    <tr>
                        <td>Tgl. lahir</td><td>:</td>
                        <td>
                            <div class="flex items-center">
                                <div class="flex">
                                    <select name="day" id="day" class="rounded text-xs pl-0 pr-7">
                                        <option value="">-</option>
                                        @for ($i = 1; $i < 32; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                    <select name="month" id="month" class="rounded text-xs pl-0 pr-7 ml-1">
                                        <option value="">-</option>
                                        @for ($i = 1; $i < 13; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                    <select name="year" id="year" class="rounded text-xs pl-0 pr-7 ml-1">
                                        <option value="">-</option>
                                        @for ($i = ((int)date("Y") - 30); $i < ((int)date("Y") + 30); $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr><td></td><td></td><td class="italic">format: dd-mm-yyyy</td></tr>
                    <tr>
                        <td>Gender</td><td>:</td>
                        <td>
                            <input type="radio" name="gender" id="pria" value="pria" class="ml-2">
                            <label for="pria" class="ml-1">pria</label>
                            <input type="radio" name="gender" id="wanita" value="wanita" class="ml-5">
                            <label for="wanita" class="ml-1">wanita</label>
                            <input type="radio" name="gender" id="none" value="" class="ml-5 hidden" checked>
                            <label for="none" class="ml-5">X</label>
                        </td>
                    </tr>
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
            <div class="grid grid-cols-2">
                <table class="mt-1">
                    <tr><td>jalan</td><td>:</td><td><input type="text" name="jalan" class="text-xs p-1 rounded"></td></tr>
                    <tr><td>komplek</td><td>:</td><td><input type="text" name="komplek" class="text-xs p-1 rounded"></td></tr>
                    <tr><td>rt</td><td>:</td><td><input type="text" name="rt" class="text-xs p-1 rounded"></td></tr>
                    <tr><td>desa</td><td>:</td><td><input type="text" name="desa" class="text-xs p-1 rounded"></td></tr>
                    <tr><td>kecamatan</td><td>:</td><td><input type="text" name="kecamatan" class="text-xs p-1 rounded"></td></tr>
                    <tr><td>kodepos</td><td>:</td><td><input type="text" name="kodepos" class="text-xs p-1 rounded"></td></tr>
                    <tr><td>kabupaten</td><td>:</td><td><input type="text" name="kabupaten" class="text-xs p-1 rounded"></td></tr>
                    <tr><td>pulau</td><td>:</td><td><input type="text" name="pulau" class="text-xs p-1 rounded"></td></tr>
                    <tr><td>(*)short(daerah)</td><td>:</td><td><input type="text" name="short" class="text-xs p-1 rounded"></td></tr>
                </table>
                <table>
                    <tr><td>rw</td><td>:</td><td><input type="text" name="rw" class="text-xs p-1 rounded"></td></tr>
                    <tr><td>kelurahan</td><td>:</td><td><input type="text" name="kelurahan" class="text-xs p-1 rounded"></td></tr>
                    <tr><td>kota</td><td>:</td><td><input type="text" name="kota" class="text-xs p-1 rounded"></td></tr>
                    <tr><td>provinsi</td><td>:</td><td><input type="text" name="provinsi" class="text-xs p-1 rounded"></td></tr>
                    <tr><td>negara</td><td>:</td><td><input type="text" name="negara" class="text-xs p-1 rounded"></td></tr>
                    <tr><td>negara</td><td>:</td><td><input type="text" name="negara" class="text-xs p-1 rounded"></td></tr>
                    <td>(*)long</td><td>:</td><td><textarea name="long" id="" cols="30" rows="4" class="border border-slate-400 rounded p-1 text-xs"></textarea></td>
                </table>
            </div>

            <div class="text-center mt-2">
                <button type="submit" class="bg-emerald-500 rounded text-white py-2 px-5 font-semibold">+ Pelanggan Baru</button>
            </div>
        </form>
    </div>
    <x-errors-any></x-errors-any>
    <x-validation-feedback></x-validation-feedback>

    {{-- <div>
        <label for="">Opsional:</label><br>
        <button type="button" class="border rounded-lg border-sky-300 text-sky-500 px-1" id="btn-initial" onclick="showHide('opsi-initial', this.id)">+initial</button>
        <button type="button" class="border rounded-lg border-sky-300 text-sky-500 px-1" id="btn-gender" onclick="showHide('opsi-gender', this.id)">+gender</button>
        <button type="button" class="border rounded-lg border-sky-300 text-sky-500 px-1" id="btn-nik" onclick="showHide('opsi-nik', this.id)">+NIK</button>
        <button type="button" class="border rounded-lg border-sky-300 text-sky-500 px-1" id="btn-tanggal_lahir" onclick="showHide('opsi-tanggal_lahir', this.id)">+tgl.lahir</button>
    </div> --}}
</main>

<script>
    // function showHide(toshow, tohide) {
    //     $(`#${toshow}`).show();
    //     $(`#${tohide}`).hide();
    // }
</script>

@endsection
