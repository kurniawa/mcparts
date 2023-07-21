<?php

use App\Http\Controllers\AccountingController;
use App\Http\Controllers\ArtisanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotaController;
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
    Route::get('/spks','index')->name('spks.index');
    Route::get('/spks/{spk}/show','show')->name('spks.show');
    Route::get('/spks/create','create')->name('spks.create');
    Route::post('/spks/store','store')->name('spks.store');
    Route::post('/spks/{spk_produk}/spk_item_tetapkan_selesai','spk_item_tetapkan_selesai')->name('spks.spk_item_tetapkan_selesai');
    Route::post('/spks/{spk}/delete','delete')->name('spks.delete');
});

Route::controller(NotaController::class)->group(function(){
    Route::get('/notas','index')->name('notas.index');
    Route::post('/notas/{spk}/{spk_produk}/create_or_edit_jumlah_spk_produk_nota','create_or_edit_jumlah_spk_produk_nota')->name('notas.create_or_edit_jumlah_spk_produk_nota');
    Route::post('/notas/{nota}/delete','delete')->name('notas.delete');
});

Route::controller(SrjalanController::class)->group(function(){
    Route::get('/sjs','index')->name('sjs.index');
    Route::post('/sjs/{spk}/{spk_produk}/{spk_produk_nota}/create_or_edit_jumlah_spk_produk_nota_srjalan','create_or_edit_jumlah_spk_produk_nota_srjalan')->name('sjs.create_or_edit_jumlah_spk_produk_nota_srjalan');
});

Route::controller(AccountingController::class)->group(function(){
    Route::get('/accounting','index')->name('accounting.index');
});

Route::controller(ArtisanController::class)->group(function(){
    Route::get('/artisan-command','index')->name('artisan.index')->middleware();
    Route::post('/artisan-command/change-column-name','change_column_name')->name('artisan.change_column_name')->middleware('auth');
    Route::post('/artisan-command/lower-case-role','lower_case_role')->name('artisan.lower_case_role')->middleware('auth');
    Route::post('/artisan-command/create-spk-nota-relation','create_spk_nota_relation')->name('artisan.create_spk_nota_relation')->middleware('developer');
    Route::post('/artisan-command/create-nota-sj-relation','create_nota_srjalan_relation')->name('artisan.create_nota_srjalan_relation')->middleware('developer');
    Route::post('/artisan-command/migrate-fresh-seed','migrate_fresh_seed')->name('artisan.migrate_fresh_seed')->middleware('developer');
    Route::post('/artisan-command/symbolic-link','symbolic_link')->name('artisan.symbolic_link')->middleware('developer');
    Route::post('/artisan-command/optimize-clear','optimize_clear')->name('artisan.optimize_clear')->middleware('developer');
    Route::post('/artisan-command/spk-produk-fix-nama-produk','spk_produk_fix_nama_produk')->name('artisan.spk_produk_fix_nama_produk')->middleware('developer');
    Route::post('/artisan-command/srjalan_fix_jml_packing','srjalan_fix_jml_packing')->name('artisan.srjalan_fix_jml_packing')->middleware('developer');
});
