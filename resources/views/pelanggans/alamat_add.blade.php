@extends('layouts.main')
@section('content')
<header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <h1 class="text-xl font-bold tracking-tight text-gray-900">Alamat Baru - Pelanggan: {{ $pelanggan->nama }}</h1>
    </div>
  </header>
<main>
    <div class="flex justify-center text-xs mt-2">
        <div class="lg:w-1/2 md:w-3/4 border rounded p-1 bg-white shadow drop-shadow-sm">
            <form class="rounded" action="{{ route('pelanggans.alamat_add_post', $pelanggan->id) }}" method="POST">
                @csrf
                <table>
                    <tr><th>jalan</th><th>:</th><td><input type="text" name="jalan" class="text-xs p-1 rounded"></td></tr>
                    <tr><th>komplek</th><th>:</th><td><input type="text" name="komplek" class="text-xs p-1 rounded"></td></tr>
                    <tr><th>rt</th><th>:</th><td><input type="text" name="rt" class="text-xs p-1 rounded"></td><th>rw</th><th>:</th><td><input type="text" name="rw" class="text-xs p-1 rounded" ></td></tr>
                    <tr><th>desa</th><th>:</th><td><input type="text" name="desa" class="text-xs p-1 rounded"></td><th>kelurahan</th><th>:</th><td><input type="text" name="kelurahan" class="text-xs p-1 rounded"></td></tr>
                    <tr><th>kecamatan</th><th>:</th><td><input type="text" name="kecamatan" class="text-xs p-1 rounded"></td><th>kota</th><th>:</th><td><input type="text" name="kota" class="text-xs p-1 rounded"></td></tr>
                    <tr><th>kodepos</th><th>:</th><td><input type="text" name="kodepos" class="text-xs p-1 rounded"></td></tr>
                    <tr><th>kabupaten</th><th>:</th><td><input type="text" name="kabupaten" class="text-xs p-1 rounded"></td><th>provinsi</th><th>:</th><td><input type="text" name="provinsi" class="text-xs p-1 rounded"></td></tr>
                    <tr><th>pulau</th><th>:</th><td><input type="text" name="pulau" class="text-xs p-1 rounded"></td><th>negara</th><th>:</th><td><input type="text" name="negara" class="text-xs p-1 rounded"></td></tr>
                    <tr><th>(*)short(daerah)</th><th>:</th><td><input type="text" name="short" class="text-xs p-1 rounded"></td><th>(*)long</th><th>:</th><td><textarea name="long" id="" cols="30" rows="4" class="border border-slate-400 rounded p-1 text-xs"></textarea></td></tr>
                </table>
                <div class="text-center mt-2">
                    <button type="submit" class="bg-emerald-500 rounded text-white py-2 px-5 font-semibold">+ Alamat</button>
                </div>
            </form>
        </div>
    </div>
    <x-errors-any></x-errors-any>
    <x-validation-feedback></x-validation-feedback>
</main>


@endsection
