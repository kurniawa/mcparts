<?php

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
    Route::get('/profile','profile')->name('user.profile')->middleware('auth');
});

Route::controller(SpkController::class)->group(function(){
    Route::get('/spks','index')->name('spks');
});

Route::controller(NotaController::class)->group(function(){
    Route::get('/notas','index')->name('notas');
});

Route::controller(SrjalanController::class)->group(function(){
    Route::get('/sjs','index')->name('sjs');
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
});
