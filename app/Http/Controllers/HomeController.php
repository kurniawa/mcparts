<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Nota;
use App\Models\NotaSrjalan;
use App\Models\Produk;
use App\Models\Spk;
use App\Models\SpkNota;
use App\Models\SpkProduk;
use App\Models\SpkProdukNota;
use App\Models\SpkProdukNotaSrjalan;
use App\Models\Srjalan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    function home(Request $request) {
        // $user_role = Auth::user()->role;
        $get = $request->query();
        dump($get);

        $spks = collect();
        $col_spk_produks = collect();
        $col_notas = collect();
        $col_spk_produk_notas = collect();
        $col_srjalans = collect();
        $col_spk_produk_nota_srjalans = collect();

        if (isset($get['tipe_filter'])) {
            if ($get['nama_pelanggan'] === null) {
                if ($get['from_day'] === null || $get['from_month'] === null || $get['from_year'] === null || $get['to_date'] === null || $get['to_month'] === null || $get['to_year'] === null) {
                    // Filter Berdasarkan Nama Pelanggan - Tanpa Tanggal
                    // End - Filter Berdasarkan Nama Pelanggan - Tanpa Tanggal
                } else {
                    // Filter Berdasarkan Nama Pelanggan + Tanggal
                    // End - Filter Berdasarkan Nama Pelanggan + Tanggal
                }
            }
            if ($get['tipe_filter'] === 'spk') {
                # code...
            } elseif ($get['tipe_filter'] === 'nota') {
                # code...
            } elseif ($get['tipe_filter'] === 'sj') {
                # code...
            } else {
                $request->validate(['error'=>'required'],['error.required'=>'tipe_filter...']);
            }
        } else {
            $spks = Spk::latest()->limit(20)->get();
        }

        foreach ($spks as $spk) {
            // SPK Items
            $spk_produks = SpkProduk::where('spk_id', $spk->id)->get();
            $col_spk_produks->push($spk_produks);
            // END - SPK Items
            $spk_notas = SpkNota::where('spk_id', $spk->id)->get();
            $notas = collect();
            $arr_srjalans = collect();

            $spk_produk_notas = collect();
            $spk_produk_nota_srjalans = collect();
            foreach ($spk_notas as $spk_nota) {
                $nota = Nota::find($spk_nota->nota_id);
                $notas->push($nota);
                $nota_srjalans = NotaSrjalan::where('nota_id', $nota->id)->get();
                $spk_produk_notas->push(SpkProdukNota::where('nota_id', $nota->id)->get());
                $srjalans = collect();
                foreach ($nota_srjalans as $nota_srjalan) {
                    $srjalan = Srjalan::find($nota_srjalan->srjalan_id);
                    $srjalans->push($srjalan);
                    $spk_produk_nota_srjalans->push(SpkProdukNotaSrjalan::where('srjalan_id', $srjalan->id)->get());
                }
                $arr_srjalans->push($srjalans);
            }
            $col_notas->push($notas);
            $col_srjalans->push($arr_srjalans);
            $col_spk_produk_notas->push($spk_produk_notas);
            $col_spk_produk_nota_srjalans->push($spk_produk_nota_srjalans);
        }

        // dump($col_notas);
        // dd($col_srjalans);
        $data = [
            // 'goback' => 'home',
            // 'user_role' => $user_role,
            'menus' => Menu::get(),
            'route_now' => 'home',
            'profile_menus' => Menu::get_profile_menus(),
            'spks' => $spks,
            'col_notas' => $col_notas,
            'col_srjalans' => $col_srjalans,
            'col_spk_produks' => $col_spk_produks,
            'col_spk_produk_notas' => $col_spk_produk_notas,
            'col_spk_produk_nota_srjalans' => $col_spk_produk_nota_srjalans,
        ];
        // dump($user_role);
        // dump($spks);
        // dd($data);
        // dd($col_spk_produk_notas[0]);
        return view('app', $data);
    }

    function info() {
        $data = [
            'goback' => 'home',
        ];
        return view('about.index', $data);
    }
}
