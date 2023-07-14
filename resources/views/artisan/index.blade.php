@extends('layouts.main')
@section('content')
<header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <h1 class="text-3xl font-bold tracking-tight text-gray-900">Artisan Command Center</h1>
    </div>
  </header>
  <main>
      <form action="{{ route('artisan.lower_case_role') }}" method="post" class="ml-2">
          @csrf
          <p>Sebelumnya, ganti nama column clearance menjadi role terlebih dahulu.</p>
          <button class="bg-pink-400 text-white font-semibold rounded px-3 py-2">Lower Case Role</button>
      </form>
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8 flex">
        <form action="{{ route('artisan.create_spk_nota_relation') }}" method="post" class="flex ml-2">
            @csrf
            <button class="bg-orange-400 text-white font-semibold rounded px-3 py-2">SPK-Nota Relation</button>
        </form>
        <form action="{{ route('artisan.create_nota_srjalan_relation') }}" method="post" class="flex ml-2">
            @csrf
            <button class="bg-emerald-400 text-white font-semibold rounded px-3 py-2">Nota-SJ Relation</button>
        </form>
        <form action="{{ route('artisan.symbolic_link') }}" method="post" class="flex ml-2">
            @csrf
            <button class="bg-pink-400 text-white font-semibold rounded px-3 py-2">storage:link</button>
        </form>
        <form action="{{ route('artisan.optimize_clear') }}" method="post" class="flex ml-2">
            @csrf
            <button class="bg-emerald-400 text-white font-semibold rounded px-3 py-2">optimize:clear</button>
        </form>
        {{-- <form action="{{ route('artisan.change_column_name') }}" method="post" class="flex ml-2">
            @csrf
            <input type="hidden" name="column_name_old" value="clearance">
            <input type="hidden" name="column_name_new" value="role">
            <button class="bg-orange-400 text-white font-semibold rounded px-3 py-2" type="submit" name="table_name" value="users">User:clearance->role</button>
        </form>
        <form action="{{ route('artisan.migrate_fresh_seed') }}" method="post" class="flex">
            @csrf
            <button class="bg-orange-400 text-white font-semibold rounded px-3 py-2">migrate:fresh --seed</button>
        </form> --}}
    </div>
    <div class="ml-2 mt-5">
        <form action="{{ route('artisan.spk_produk_fix_nama_produk') }}" method="post">
            @csrf
            <button class="bg-emerald-400 text-white font-semibold rounded px-3 py-2">Spk Produk: NamaProduk harus ada</button>
            <p>Setelah Nama Produk ada, masuk ke phpmyadmin untuk edit column nama_produk tidak nullable</p>
        </form>
    </div>
    {{-- <div class="ml-2 mt-5">
        <form action="{{ route('artisan.spk_produk_fix_nama_produk') }}" method="post">
            @csrf
            <p>Edit dulu struktur column spk_produk_nota_srjalans, yakni tambah column nama_nota</p>
            <p>Setelah itu tekan tombol nya supaya diisi nama_nota nya</p>
            <button class="bg-emerald-400 text-white font-semibold rounded px-3 py-2">Lengkapi nama_nota pada spk_produk_nota_srjalans</button>
        </form>
    </div> --}}
    <x-errors-any></x-errors-any>
    <x-validation-feedback></x-validation-feedback>
  </main>
</div>
@endsection
