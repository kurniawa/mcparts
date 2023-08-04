<?php

use App\Http\Controllers\AccountingController;
use App\Http\Controllers\ArtisanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotaController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\SpkController;
use App\Http\Controllers\SrjalanController;
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
    Route::post('/user/profile/update_nama','update_nama')->name('user.profile.update_nama')->middleware('auth');
    Route::post('/user/profile/update_password','update_password')->name('user.profile.update_password')->middleware('auth');
    Route::post('/user/profile/update_photo','update_photo')->name('user.profile.update_photo')->middleware('auth');
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
    Route::post('/sjs/{srjalan}/edit_ekspedisi','edit_ekspedisi')->name('sjs.edit_ekspedisi')->middleware('auth');
    Route::post('/sjs/{srjalan}/edit_transit','edit_transit')->name('sjs.edit_transit')->middleware('auth');
});

Route::controller(PelangganController::class)->group(function(){
    Route::get('/pelanggans','index')->name('pelanggans.index');
});

Route::controller(AccountingController::class)->group(function(){
    Route::get('/accounting','index')->name('accounting.index');
});

Route::controller(ArtisanController::class)->group(function(){
    Route::get('/artisan-command','index')->name('artisan.index')->middleware('auth');
    Route::post('/artisan-command/change-column-name','change_column_name')->name('artisan.change_column_name')->middleware('auth');
    Route::post('/artisan-command/lower-case-role','lower_case_role')->name('artisan.lower_case_role')->middleware('auth');
    Route::post('/artisan-command/create-spk-nota-relation','create_spk_nota_relation')->name('artisan.create_spk_nota_relation')->middleware('developer');
    Route::post('/artisan-command/create-nota-sj-relation','create_nota_srjalan_relation')->name('artisan.create_nota_srjalan_relation')->middleware('developer');
    Route::post('/artisan-command/migrate-fresh-seed','migrate_fresh_seed')->name('artisan.migrate_fresh_seed')->middleware('developer');
    Route::post('/artisan-command/symbolic-link','symbolic_link')->name('artisan.symbolic_link')->middleware('developer');
    Route::post('/artisan-command/optimize-clear','optimize_clear')->name('artisan.optimize_clear')->middleware('developer');
    Route::post('/artisan-command/spk-produk-fix-nama-produk','spk_produk_fix_nama_produk')->name('artisan.spk_produk_fix_nama_produk')->middleware('developer');
    Route::post('/artisan-command/srjalan_fix_jumlah_packing','srjalan_fix_jumlah_packing')->name('artisan.srjalan_fix_jumlah_packing')->middleware('developer');
    Route::post('/artisan-command/create_table_tipe_packing','create_table_tipe_packing')->name('artisan.create_table_tipe_packing')->middleware('developer');
});
