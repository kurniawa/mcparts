@extends('layouts.main')
@section('content')
<header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <h1 class="text-xl font-bold tracking-tight text-gray-900">Artisan Command Center</h1>
    </div>
  </header>
  <x-errors-any></x-errors-any>
  <x-validation-feedback></x-validation-feedback>
  <main class="text-xs">
    <form action="{{ route('artisan.filling_suppliers_dan_barangs') }}" method="post" class="ml-2">
        @csrf
        <button class="bg-pink-400 text-white font-semibold rounded px-3 py-2">filling_suppliers_dan_barangs</button>
    </form>
      <form action="{{ route('artisan.lower_case_role') }}" method="post" class="ml-2">
          @csrf
          <p>Sebelumnya, ganti nama column clearance menjadi role terlebih dahulu.</p>
          <p>Tipe ENUM jadi VARCHAR, 20. Comment: member, admin, superadmin, developer.</p>
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
    <div class="ml-2 mt-5">
        <div>
            <ol class="list-decimal">
                <li>1. Benerin SPK 284 yang 1600 item masuk ke SJ</li>
                <li>2. Edit Harga pada item nota, hitung otomatis harga_total nota</li>
            </ol>
        </div>
    </div>
    {{-- <div class="ml-2 mt-5">
        <form action="{{ route('artisan.spk_produk_fix_nama_produk') }}" method="post">
            @csrf
            <p>Edit dulu struktur column spk_produk_nota_srjalans, yakni tambah column nama_nota</p>
            <p>Setelah itu tekan tombol nya supaya diisi nama_nota nya</p>
            <button class="bg-emerald-400 text-white font-semibold rounded px-3 py-2">Lengkapi nama_nota pada spk_produk_nota_srjalans</button>
        </form>
    </div> --}}
    <div class="ml-2 mt-5">
        <form action="{{ route('artisan.srjalan_fix_jumlah_packing') }}" method="post">
            @csrf
            <p>Srjalan: Add Column: jumlah_packing -> string -> nanti value nya string json, karena tipe_packing bisa macam2: colly, dus, rol</p>
            <p>comment: type: string json</p>
            <button class="bg-indigo-400 text-white font-semibold rounded px-3 py-2">SJ: srjalan_fix_jumlah_packing</button>
            <p>Jangan dulu sampe bener2 yakin: Hapus jml_colly, jml_dus, jml_rol -> karena ini hardcoded, bad programming</p>
        </form>
    </div>

    <div class="ml-2 mt-5 flex">
        <div class="border-2 border-sky-600 rounded p-1">
            @csrf
            <p>o) spks - hapus column: cust_long_ala, cust_kontak, cust_kontak_id, reseller_long_ala, reseller_kontak, reseller_kontak_id, status_tree</p>
            <p>o) spks - jangan salah hapus column: cust_short, reseller_short, harga_total</p>
            <p>-- spks - sisa column: 22</p>
            <p>-- spks - ganti: judul->keterangan, status_sj->status_srjalan, jumlah_sudah_sj->jumlah_sudah_srjalan</p>
            <p>o) spk_produks - ganti nama column: jml_sdh_nota menjadi jumlah_sudah_nota, deviasi_jml menjadi deviasi_jumlah, jml_t menjadi jumlah_total, jml_selesai menjadi jumlah_selesai,dll</p>
            <p>-- spk_produks - hapus column: data_selesai, data_nota, data_srjalan, status_nota, status_srjalan, jml_blm_selesai, harga</p>
            <p>-- spk_produks - sisa column: 16</p>
            <p>o) notas - ganti nama column: cust_long, reseller_alamat, reseller_kontak, dll</p>
            <p>-- notas - sisa column tetap: 25</p>
            <p>o) srjalans - ganti nama column: cust_long, reseller_alamat, reseller_kontak, dll</p>
            <p>-- srjalans - sisa column: 39</p>
            <p>o) spk_produk_nota_srjalans - ganti nama column: jml_packing->jumlah_packing</p>
            <p>o) spk_produks column jumlah_sudah_nota dan jumlah_sudah_srjalan, siap2 dihapus</p>
        </div>
    </div>

    <div class="ml-2 mt-5">
        <form action="{{ route('artisan.create_table_tipe_packing') }}" method="post">
            @csrf
            <button class="bg-orange-400 text-white font-semibold rounded px-3 py-2">create_table_tipe_packing</button>
        </form>
    </div>

    <div class="ml-2 mt-5 flex">
        <div class="border-2 border-sky-600 rounded p-1">
            @csrf
            <p>o) produk_harga - edit column status -> VARCHAR 20, not null, default: default, comment: default, lama</p>
            <p>o) pelanggan_produk - edit column status -> VARCHAR 20, not null, default: default, comment: default, lama</p>
        </div>
    </div>

    <div class="ml-2 mt-5 flex">
        <div class="border-2 border-sky-600 rounded p-1">
            @csrf
            <h3 class="text-bold font-md">Table: pelanggans</h3>
            <p>o) hapus: nama_organisasi, nama_toko, nama_pemilik, alias</p>
            <p>o) sisa column: 18</p>
        </div>
    </div>
    {{-- SUPPLIER --}}
    <div class="flex items-center bg-white rounded shadow drop-shadow p-1">
        <h5 class="font-semibold ml-2">Supplier</h5>
    </div>
    <div class="ml-2 mt-5">
        <form action="{{ route('artisan.duplicate_pembelian_temps') }}" method="post">
            @csrf
            <button class="bg-violet-400 text-white font-semibold rounded px-3 py-2">duplicate_pembelian_temps</button>
        </form>
        <form action="{{ route('artisan.create_table_supplier_barang') }}" method="post" class="mt-1">
            @csrf
            <button class="bg-orange-400 text-white font-semibold rounded px-3 py-2">create_table_supplier_barang</button>
        </form>
        <form action="{{ route('artisan.reset_schema_table_pembelian') }}" method="post" class="mt-1">
            @csrf
            <button class="bg-emerald-400 text-white font-semibold rounded px-3 py-2">reset_schema_table_pembelian</button>
        </form>
        <form action="{{ route('artisan.create_table_pembelian_barangs') }}" method="post" class="mt-1">
            @csrf
            <button class="bg-pink-400 text-white font-semibold rounded px-3 py-2">create_table_pembelian_barangs</button>
        </form>
        <form action="{{ route('artisan.filling_pembelian_barang') }}" method="post" class="mt-1">
            @csrf
            <button class="bg-indigo-400 text-white font-semibold rounded px-3 py-2">filling_pembelian_barang</button>
        </form>
    </div>
    {{-- END - SUPPLIER --}}
    {{-- PRODUK --}}
    <div class="flex items-center bg-white rounded shadow drop-shadow p-1 mt-5">
        <h5 class="font-semibold ml-2">Produk</h5>
    </div>
    <div class="ml-2 mt-1">
        tambah column pada table produks:
        <ul>
            <li>supplier_nama : varchar(50)->nullable()</li>
            <li>supplier_id : foreignId, nullable, constrained, ondelete set null, unsigned</li>
        </ul>
    </div>
    {{-- END - PRODUK --}}
    {{-- ACCOUNTING --}}
    <div class="flex items-center bg-white rounded shadow drop-shadow p-1 mt-5">
        <h5 class="font-semibold ml-2">ACCOUNTING</h5>
    </div>
    <div class="ml-2 mt-1">
        <form action="{{ route('artisan.create_tables_for_accounting') }}" method="post">
            @csrf
            <button class="bg-violet-400 text-white font-semibold rounded px-3 py-2">create_tables_for_accounting</button>
        </form>
    </div>
    <div class="ml-2 mt-1">
        <form action="{{ route('artisan.create_table_for_transaction_names') }}" method="post">
            @csrf
            <button class="bg-violet-400 text-white font-semibold rounded px-3 py-2">create_table_for_transaction_names</button>
        </form>
    </div>
    {{-- END - ACCOUNTING --}}
    <div class="h-16"></div>
  </main>
</div>
@endsection
