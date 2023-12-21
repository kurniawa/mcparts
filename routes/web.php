<?php

use App\Http\Controllers\AccountingController;
use App\Http\Controllers\ArtisanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\EkspedisiController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotaController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\ProdukPhotoController;
use App\Http\Controllers\SpkController;
use App\Http\Controllers\SrjalanController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('app');
// })->middleware('auth');

Route::controller(HomeController::class)->group(function(){
    Route::get('/','home')->name('home')->middleware('auth');
    Route::get('/settings','settings')->name('settings')->middleware('auth');
});

Route::controller(AuthController::class)->group(function(){
    Route::get('/login','login')->name('login');
    Route::post('/login','authenticate')->name('authenticate');
    Route::post('/logout','logout')->name('logout');
    Route::get('/register','register')->name('register');
    Route::post('/register','register_new')->name('register_new');
});

Route::controller(UserController::class)->group(function(){
    Route::get('/user/profile','profile')->name('user.profile')->middleware('auth');
    Route::post('/user/profile/{user}/update_nama','update_nama')->name('user.profile.update_nama')->middleware('auth');
    Route::post('/user/profile/{user}/update_username','update_username')->name('user.profile.update_username')->middleware('auth');
    Route::post('/user/profile/{user}/update_password','update_password')->name('user.profile.update_password')->middleware('auth');
    Route::post('/user/profile/{user}/update_photo','update_photo')->name('user.profile.update_photo')->middleware('auth');
    Route::post('/user/profile/{user}/delete_profile_picture','delete_profile_picture')->name('user.profile.delete_profile_picture')->middleware('auth');
});

Route::controller(SpkController::class)->group(function(){
    // Route::get('/spks','index')->name('spks.index')->middleware('auth');
    Route::get('/spks/{spk}/show','show')->name('spks.show')->middleware('auth');
    Route::get('/spks/{spk}/print_out','print_out')->name('spks.print_out')->middleware('auth');
    Route::get('/spks/create','create')->name('spks.create')->middleware('auth');
    Route::post('/spks/{spk}/edit_keterangan','edit_keterangan')->name('spks.edit_keterangan')->middleware('auth');
    Route::post('/spks/{spk}/edit_pelanggan','edit_pelanggan')->name('spks.edit_pelanggan')->middleware('auth');
    Route::post('/spks/{spk}/edit_tanggal','edit_tanggal')->name('spks.edit_tanggal')->middleware('auth');
    Route::post('/spks/store','store')->name('spks.store')->middleware('auth');
    Route::post('/spks/{spk_produk}/spk_item_tetapkan_selesai','spk_item_tetapkan_selesai')->name('spks.spk_item_tetapkan_selesai')->middleware('auth');
    Route::post('/spks/{spk}/delete','delete')->name('spks.delete')->middleware('auth');
    Route::post('/spks/{spk}/selesai_all','selesai_all')->name('spks.selesai_all')->middleware('auth');
    Route::post('/spks/{spk}/add_item','add_item')->name('spks.add_item')->middleware('auth');
    Route::post('/spks/{spk}/{spk_produk}/delete_item','delete_item')->name('spks.delete_item')->middleware('auth');
    Route::post('/spks/{spk}/{spk_produk}/edit_jumlah_deviasi','edit_jumlah_deviasi')->name('spks.edit_jumlah_deviasi')->middleware('auth');
    Route::post('/spks/{spk}/{spk_produk}/spk_produk_edit_keterangan','spk_produk_edit_keterangan')->name('spks.spk_produk_edit_keterangan')->middleware('auth');
    Route::post('/spks/{spk}/{spk_produk_nota}/spk_produk_nota_edit_keterangan','spk_produk_nota_edit_keterangan')->name('spks.spk_produk_nota_edit_keterangan')->middleware('auth');
});

Route::controller(NotaController::class)->group(function(){
    Route::get('/notas/{nota}/print_out','print_out')->name('notas.print_out');
    Route::post('/notas/{spk}/{spk_produk}/create_or_edit_jumlah_spk_produk_nota','create_or_edit_jumlah_spk_produk_nota')->name('notas.create_or_edit_jumlah_spk_produk_nota')->middleware('auth');
    Route::post('/notas/{spk}/{nota}/delete','delete')->name('notas.delete')->middleware('auth');
    Route::post('/notas/{spk}/nota_all','nota_all')->name('notas.nota_all')->middleware('auth');
    Route::post('/notas/{nota}/edit_tanggal','edit_tanggal')->name('notas.edit_tanggal')->middleware('auth');
    Route::post('/notas/{spk}/{spk_produk_nota}/delete_item','delete_item')->name('notas.delete_item')->middleware('auth');
    Route::post('/notas/{spk}/edit_alamat','edit_alamat')->name('notas.edit_alamat')->middleware('auth');
    Route::post('/notas/{spk}/edit_kontak','edit_kontak')->name('notas.edit_kontak')->middleware('auth');
    Route::post('/notas/{spk}/{nota}/{spk_produk_nota}/edit_harga_item','edit_harga_item')->name('notas.edit_harga_item')->middleware('auth');
});

Route::controller(SrjalanController::class)->group(function(){
    Route::get('/sjs/{srjalan}/print_out','print_out')->name('sjs.print_out');
    Route::post('/sjs/{spk}/{nota}/{spk_produk}/{spk_produk_nota}/create_or_edit_jumlah_spk_produk_nota_srjalan','create_or_edit_jumlah_spk_produk_nota_srjalan')->name('sjs.create_or_edit_jumlah_spk_produk_nota_srjalan')->middleware('auth');
    Route::post('/sjs/{srjalan}/edit_tanggal','edit_tanggal')->name('sjs.edit_tanggal')->middleware('auth');
    Route::post('/sjs/{srjalan}/{spk_produk_nota_srjalan}/edit_jumlah_packing','edit_jumlah_packing')->name('sjs.edit_jumlah_packing')->middleware('auth');
    Route::post('/sjs/{spk}/{srjalan}/delete','delete')->name('sjs.delete')->middleware('auth');
    Route::post('/sjs/{spk}/{nota}/srjalan_all','srjalan_all')->name('sjs.srjalan_all')->middleware('auth');
    Route::post('/sjs/{spk}/{srjalan}/{spk_produk_nota_srjalan}/delete_item','delete_item')->name('sjs.delete_item')->middleware('auth');
    Route::post('/sjs/{srjalan}/update_packing','update_packing')->name('sjs.update_packing')->middleware('auth');
    Route::post('/sjs/{srjalan}/edit_jenis_barang','edit_jenis_barang')->name('sjs.edit_jenis_barang')->middleware('auth');
    Route::post('/sjs/{spk}/edit_ekspedisi','edit_ekspedisi')->name('sjs.edit_ekspedisi')->middleware('auth');
    Route::post('/sjs/{spk}/edit_transit','edit_transit')->name('sjs.edit_transit')->middleware('auth');
    Route::post('/sjs/{srjalan}/edit_nama_tertera','edit_nama_tertera')->name('sjs.edit_nama_tertera')->middleware('auth');
});

Route::controller(PelangganController::class)->group(function(){
    Route::get('/pelanggans','index')->name('pelanggans.index');
    Route::get('/pelanggans/create','create')->name('pelanggans.create');
    Route::post('/pelanggans/store','store')->name('pelanggans.store');
    Route::post('/pelanggans/{pelanggan}/delete','delete')->name('pelanggans.delete');
    Route::get('/pelanggans/{pelanggan}/show','show')->name('pelanggans.show')->middleware('auth');
    Route::post('/pelanggans/{pelanggan}/kontak_add','kontak_add')->name('pelanggans.kontak_add')->middleware('auth');
    Route::post('/pelanggans/{pelanggan_kontak}/kontak_edit','kontak_edit')->name('pelanggans.kontak_edit')->middleware('auth');
    Route::post('/pelanggans/{pelanggan_kontak}/kontak_delete','kontak_delete')->name('pelanggans.kontak_delete')->middleware('auth');
    Route::post('/pelanggans/{pelanggan}/{pelanggan_kontak}/kontak_utama','kontak_utama')->name('pelanggans.kontak_utama')->middleware('auth');
    Route::get('/pelanggans/{pelanggan}/alamat_add','alamat_add')->name('pelanggans.alamat_add')->middleware('auth');
    Route::post('/pelanggans/{pelanggan}/alamat_add','alamat_add_post')->name('pelanggans.alamat_add_post')->middleware('auth');
    Route::post('/pelanggans/{alamat}/delete_alamat','delete_alamat')->name('pelanggans.delete_alamat')->middleware('auth');
    Route::get('/pelanggans/{pelanggan}/{alamat}/alamat_edit','alamat_edit')->name('pelanggans.alamat_edit')->middleware('auth');
    Route::post('/pelanggans/{pelanggan}/{alamat}/alamat_edit','alamat_edit_post')->name('pelanggans.alamat_edit_post')->middleware('auth');
    Route::post('/pelanggans/{pelanggan}/{alamat}/alamat_utama','alamat_utama')->name('pelanggans.alamat_utama')->middleware('auth');
    Route::post('/pelanggans/{pelanggan}/ekspedisi_add','ekspedisi_add')->name('pelanggans.ekspedisi_add')->middleware('auth');
    Route::post('/pelanggans/{pelanggan_ekspedisi}/ekspedisi_delete','ekspedisi_delete')->name('pelanggans.ekspedisi_delete')->middleware('auth');
    Route::post('/pelanggans/{pelanggan}/{pelanggan_ekspedisi}/ekspedisi_utama','ekspedisi_utama')->name('pelanggans.ekspedisi_utama')->middleware('auth');
    Route::post('/pelanggans/{pelanggan}/transit_add','transit_add')->name('pelanggans.transit_add')->middleware('auth');
    Route::post('/pelanggans/{pelanggan_ekspedisi}/transit_delete','transit_delete')->name('pelanggans.transit_delete')->middleware('auth');
    Route::post('/pelanggans/{pelanggan}/{pelanggan_ekspedisi}/transit_utama','transit_utama')->name('pelanggans.transit_utama')->middleware('auth');
    Route::post('/pelanggans/{pelanggan}/update_nama','update_nama')->name('pelanggans.update_nama')->middleware('auth');
});

Route::controller(EkspedisiController::class)->group(function(){
    Route::get('/ekspedisis','index')->name('ekspedisis.index');
    Route::get('/ekspedisis/{ekspedisi}/show','show')->name('ekspedisis.show');
    Route::post('/ekspedisis/store','store')->name('ekspedisis.store');
    Route::post('/ekspedisis/{ekspedisi}/delete','delete')->name('ekspedisis.delete');
    Route::post('/ekspedisis/{ekspedisi}/alamat_add','alamat_add')->name('ekspedisis.alamat_add');
    Route::post('/ekspedisis/{ekspedisi}/{alamat}/alamat_utama','alamat_utama')->name('ekspedisis.alamat_utama');
    Route::post('/ekspedisis/{ekspedisi}/{alamat}/alamat_edit','alamat_edit')->name('ekspedisis.alamat_edit')->middleware('auth');
    Route::post('/ekspedisis/{ekspedisi}/{alamat}/alamat_delete','alamat_delete')->name('ekspedisis.alamat_delete')->middleware('auth');
    Route::post('/ekspedisis/{ekspedisi}/kontak_add','kontak_add')->name('ekspedisis.kontak_add')->middleware('auth');
    Route::post('/ekspedisis/{ekspedisi_kontak}/kontak_edit','kontak_edit')->name('ekspedisis.kontak_edit')->middleware('auth');
    Route::post('/ekspedisis/{ekspedisi_kontak}/kontak_delete','kontak_delete')->name('ekspedisis.kontak_delete')->middleware('auth');
    Route::post('/ekspedisis/{ekspedisi}/{ekspedisi_kontak}/kontak_utama','kontak_utama')->name('ekspedisis.kontak_utama')->middleware('auth');
});

Route::controller(ProdukController::class)->group(function(){
    Route::get('/produks','index')->name('produks.index');
    Route::get('/produks/{produk}/show','show')->name('produks.show');
    Route::post('/produks/store','store')->name('produks.store');
    Route::post('/produks/{produk}/update','update')->name('produks.update');
    Route::post('/produks/{produk}/delete','delete')->name('produks.delete');
});

Route::controller(ProdukPhotoController::class)->group(function(){
    Route::post('/produks/{produk}/store_photo','store_photo')->name('produks.store_photo');
    Route::post('/produks/{produk}/{photo}/update_photo','update_photo')->name('produks.update_photo');
    Route::post('/produks/{produk}/{produk_photo}/{photo}/delete_photo','delete_photo')->name('produks.delete_photo');
});

Route::controller(PenjualanController::class)->group(function(){
    Route::get('/penjualans','index')->name('penjualans.index');
});

Route::controller(PembelianController::class)->group(function(){
    Route::get('/pembelians','index')->name('pembelians.index');
    Route::get('/pembelians/{pembelian}/show','show')->name('pembelians.show');
    Route::get('/pembelians/{pembelian}/edit','edit')->name('pembelians.edit');
    Route::post('/pembelians/{pembelian}/update','update')->name('pembelians.update');
    Route::post('/pembelians','store')->name('pembelians.store');
    Route::post('/pembelians/{pembelian}/delete','delete')->name('pembelians.delete');
    Route::post('/pembelians/{pembelian}/pelunasan','pelunasan')->name('pembelians.pelunasan');
    Route::post('/pembelians/{pembelian}/pembatalan_pelunasan','pembatalan_pelunasan')->name('pembelians.pembatalan_pelunasan');
    Route::post('/pembelians/{pembelian}/{pembelian_barang}/delete_pembelian_barang','delete_pembelian_barang')->name('pembelians.delete_pembelian_barang');
});

Route::controller(BarangController::class)->group(function(){
    Route::get('/barangs','index')->name('barangs.index');
    Route::get('/barangs/{barang}/show','show')->name('barangs.show');
    Route::get('/barangs/{barang}/edit','edit')->name('barangs.edit');
    Route::post('/barangs/{barang}/update','update')->name('barangs.update');
    Route::post('/barangs','store')->name('barangs.store');
    Route::post('/barangs/{barang}/delete','delete')->name('barangs.delete');
});

Route::controller(SupplierController::class)->group(function(){
    Route::get('/suppliers','index')->name('suppliers.index');
    Route::get('/suppliers/{supplier}/show','show')->name('suppliers.show');
    Route::post('/suppliers','store')->name('suppliers.store');
    Route::post('/suppliers/{supplier}/delete','delete')->name('suppliers.delete');
    Route::post('/suppliers/{supplier}/alamat_add','alamat_add')->name('suppliers.alamat_add');
    Route::post('/suppliers/{supplier}/{alamat}/alamat_utama','alamat_utama')->name('suppliers.alamat_utama');
    Route::post('/suppliers/{supplier}/{alamat}/alamat_edit','alamat_edit')->name('suppliers.alamat_edit')->middleware('auth');
    Route::post('/suppliers/{supplier}/{alamat}/alamat_delete','alamat_delete')->name('suppliers.alamat_delete')->middleware('auth');
    Route::post('/suppliers/{supplier}/kontak_add','kontak_add')->name('suppliers.kontak_add')->middleware('auth');
    Route::post('/suppliers/{supplier_kontak}/kontak_edit','kontak_edit')->name('suppliers.kontak_edit')->middleware('auth');
    Route::post('/suppliers/{supplier_kontak}/kontak_delete','kontak_delete')->name('suppliers.kontak_delete')->middleware('auth');
    Route::post('/suppliers/{supplier}/{supplier_kontak}/kontak_utama','kontak_utama')->name('suppliers.kontak_utama')->middleware('auth');
});

Route::controller(AccountingController::class)->group(function(){
    Route::get('/accounting','index')->name('accounting.index')->middleware('auth');
    Route::post('/accounting/create_kas','create_kas')->name('accounting.create_kas');
    Route::get('/accounting/{user_instance}/show_transactions','show_transactions')->name('accounting.show_transactions')->middleware('auth');
    Route::post('/accounting/{user_instance}/store_transactions','store_transactions')->name('accounting.store_transactions')->middleware('auth');
    // Route::get('/accounting/{user_instance}/store_pilih_transaction_name','store_pilih_transaction_name')->name('accounting.store_pilih_transaction_name')->middleware('auth');
    Route::post('/accounting/{user_instance}/{accounting}/mark_as_read_or_unread','mark_as_read_or_unread')->name('accounting.mark_as_read_or_unread')->middleware('auth');
    Route::post('/accounting/{user_instance}/{accounting}/apply_entry','apply_entry')->name('accounting.apply_entry')->middleware('auth');
    Route::post('/accounting/{user_instance}/{accounting}/edit_entry','edit_entry')->name('accounting.edit_entry')->middleware('auth');
    Route::post('/accounting/{user_instance}/{accounting}/delete_entry','delete_entry')->name('accounting.delete_entry')->middleware('auth');
    Route::get('/accounting/jurnal','jurnal')->name('accounting.jurnal')->middleware('auth');
    Route::get('/accounting/ringkasan','ringkasan')->name('accounting.ringkasan')->middleware('auth');
    Route::get('/accounting/transactions_relations','transactions_relations')->name('accounting.transactions_relations')->middleware('auth');
    Route::post('/accounting/store_transactions_relations','store_transactions_relations')->name('accounting.store_transactions_relations')->middleware('auth');
    Route::post('/accounting/{transaction_name}/delete_transaction_relation','delete_transaction_relation')->name('accounting.delete_transaction_relation')->middleware('auth');
    Route::post('/accounting/{user_instance}/{accounting}/up_down_transaction','up_down_transaction')->name('accounting.up_down_transaction')->middleware('auth');
});

Route::controller(ArtisanController::class)->group(function(){
    Route::get('/artisan-command','index')->name('artisan.index')->middleware('auth');
    // Route::post('/artisan-command/accounting_update_data_rupiah','accounting_update_data_rupiah')->name('artisan.accounting_update_data_rupiah')->middleware('auth');
    // Route::post('/artisan-command/change-column-name','change_column_name')->name('artisan.change_column_name')->middleware('auth');
    // Route::post('/artisan-command/lower-case-role','lower_case_role')->name('artisan.lower_case_role')->middleware('auth');
    // Route::post('/artisan-command/create-spk-nota-relation','create_spk_nota_relation')->name('artisan.create_spk_nota_relation')->middleware('developer');
    // Route::post('/artisan-command/create-nota-sj-relation','create_nota_srjalan_relation')->name('artisan.create_nota_srjalan_relation')->middleware('developer');
    // Route::post('/artisan-command/migrate-fresh-seed','migrate_fresh_seed')->name('artisan.migrate_fresh_seed')->middleware('developer');
    // Route::post('/artisan-command/symbolic-link','symbolic_link')->name('artisan.symbolic_link')->middleware('developer');
    // Route::post('/artisan-command/optimize-clear','optimize_clear')->name('artisan.optimize_clear')->middleware('developer');
    // Route::post('/artisan-command/spk-produk-fix-nama-produk','spk_produk_fix_nama_produk')->name('artisan.spk_produk_fix_nama_produk')->middleware('developer');
    // Route::post('/artisan-command/srjalan_fix_jumlah_packing','srjalan_fix_jumlah_packing')->name('artisan.srjalan_fix_jumlah_packing')->middleware('developer');
    // Route::post('/artisan-command/create_table_tipe_packing','create_table_tipe_packing')->name('artisan.create_table_tipe_packing')->middleware('developer');
    // Route::post('/artisan-command/duplicate_pembelian_temps','duplicate_pembelian_temps')->name('artisan.duplicate_pembelian_temps')->middleware('developer');
    // Route::post('/artisan-command/create_table_supplier_barang','create_table_supplier_barang')->name('artisan.create_table_supplier_barang')->middleware('developer');
    // Route::post('/artisan-command/reset_schema_table_pembelian','reset_schema_table_pembelian')->name('artisan.reset_schema_table_pembelian')->middleware('developer');
    // Route::post('/artisan-command/create_table_pembelian_barangs','create_table_pembelian_barangs')->name('artisan.create_table_pembelian_barangs')->middleware('developer');
    // Route::post('/artisan-command/filling_pembelian_barang','filling_pembelian_barang')->name('artisan.filling_pembelian_barang')->middleware('developer');
    // Route::post('/artisan-command/create_tables_for_accounting','create_tables_for_accounting')->name('artisan.create_tables_for_accounting')->middleware('developer');
    // Route::post('/artisan-command/create_table_for_transaction_names','create_table_for_transaction_names')->name('artisan.create_table_for_transaction_names')->middleware('developer');
    // Route::post('/artisan-command/filling_suppliers_dan_barangs','filling_suppliers_dan_barangs')->name('artisan.filling_suppliers_dan_barangs')->middleware('developer');
    Route::post('/artisan-command/keterangan_untuk_spk_produk_nota','keterangan_untuk_spk_produk_nota')->name('artisan.keterangan_untuk_spk_produk_nota')->middleware('auth');
});
