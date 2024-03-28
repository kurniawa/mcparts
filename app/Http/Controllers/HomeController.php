<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Nota;
use App\Models\NotaSrjalan;
use App\Models\Pelanggan;
use App\Models\Produk;
use App\Models\Spk;
use App\Models\SpkNota;
use App\Models\SpkProduk;
use App\Models\SpkProdukNota;
use App\Models\SpkProdukNotaSrjalan;
use App\Models\Srjalan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    function home(Request $request) {
        // $user_role = Auth::user()->role;
        $get = $request->query();
        // dump($get);

        $spks = collect();
        $nama_pelanggans = collect(); // Ini diperlukan untuk menampilkan nama reseller, apabila ada reseller.
        $col_spk_produks = collect();
        $col_notas = collect();
        $col_spk_produk_notas = collect();
        $col_srjalans = collect();
        $col_spk_produk_nota_srjalans = collect();

        if (isset($get['tipe_filter'])) {
            if ($get['tipe_filter'] === 'spk') {
                if ($get['nama_pelanggan'] !== null) {
                    if ($get['from_day'] === null || $get['from_month'] === null || $get['from_year'] === null || $get['to_day'] === null || $get['to_month'] === null || $get['to_year'] === null) {
                        // Filter Berdasarkan Nama Pelanggan - Tanpa Tanggal
                        $spks = Spk::where('pelanggan_id', $get['pelanggan_id'])->orderByDesc('created_at')->get();
                        // End - Filter Berdasarkan Nama Pelanggan - Tanpa Tanggal
                    } else {
                        // Filter Berdasarkan Nama Pelanggan + Tanggal
                        $start_date = "$get[from_year]-$get[from_month]-$get[from_day]";
                        $end_date = "$get[to_year]-$get[to_month]-$get[to_day] 23:59:59";
                        $spks = Spk::where('pelanggan_id', $get['pelanggan_id'])->whereBetween('created_at', [$start_date, $end_date])->orderByDesc('created_at')->get();
                        // End - Filter Berdasarkan Nama Pelanggan + Tanggal
                    }
                } else {
                    // Filter hanya rentang waktu, tanpa nama_pelanggan
                    if ($get['from_day'] === null || $get['from_month'] === null || $get['from_year'] === null || $get['to_day'] === null || $get['to_month'] === null || $get['to_year'] === null) {
                        $request->validate(['error'=>'required'],['error.required'=>'customer,time_range']);
                    } else {
                        // Filter Berdasarkan Tanggal
                        $start_date = "$get[from_year]-$get[from_month]-$get[from_day]";
                        $end_date = "$get[to_year]-$get[to_month]-$get[to_day] 23:59:59";
                        $spks = Spk::whereBetween('created_at', [$start_date, $end_date])->orderByDesc('created_at')->get();
                        // End - Filter Berdasarkan Tanggal
                    }
                    // END - Filter hanya rentang waktu, tanpa nama_pelanggan
                }
            } elseif ($get['tipe_filter'] === 'nota') {
                if ($get['nama_pelanggan'] !== null) {
                    if ($get['from_day'] === null || $get['from_month'] === null || $get['from_year'] === null || $get['to_day'] === null || $get['to_month'] === null || $get['to_year'] === null) {
                        // Filter Berdasarkan Nama Pelanggan - Tanpa Tanggal
                        $notas = Nota::where('pelanggan_id', $get['pelanggan_id'])->orderByDesc('created_at')->get();

                        // End - Filter Berdasarkan Nama Pelanggan - Tanpa Tanggal
                    } else {
                        // Filter Berdasarkan Nama Pelanggan + Tanggal
                        $start_date = "$get[from_year]-$get[from_month]-$get[from_day]";
                        $end_date = "$get[to_year]-$get[to_month]-$get[to_day] 23:59:59";
                        $notas = Nota::where('pelanggan_id', $get['pelanggan_id'])->whereBetween('created_at', [$start_date, $end_date])->orderByDesc('created_at')->get();
                        // End - Filter Berdasarkan Nama Pelanggan + Tanggal
                    }
                } else {
                    // Filter hanya rentang waktu, tanpa nama_pelanggan
                    if ($get['from_day'] === null || $get['from_month'] === null || $get['from_year'] === null || $get['to_day'] === null || $get['to_month'] === null || $get['to_year'] === null) {
                        $request->validate(['error'=>'required'],['error.required'=>'customer || time_range']);
                    } else {
                        // Filter Berdasarkan Tanggal
                        $start_date = "$get[from_year]-$get[from_month]-$get[from_day]";
                        $end_date = "$get[to_year]-$get[to_month]-$get[to_day] 23:59:59";
                        $notas = Nota::whereBetween('created_at', [$start_date, $end_date])->orderByDesc('created_at')->get();
                        // End - Filter Berdasarkan Tanggal
                    }
                    // END - Filter hanya rentang waktu, tanpa nama_pelanggan
                }
            } elseif ($get['tipe_filter'] === 'sj') {
                # code...
            } else {
                $request->validate(['error'=>'required'],['error.required'=>'tipe_filter...']);
            }
        } else {
            $spks = Spk::latest()->limit(200)->get();
        }
        // dd($spks);

        foreach ($spks as $spk) {
            // dd($spk::user($spk->created_by));
            // SPK Items
            $spk_produks = SpkProduk::where('spk_id', $spk->id)->get();
            $col_spk_produks->push($spk_produks);
            // END - SPK Items

            // NAMA PELANGGANS
            $nama_pelanggan = $spk->pelanggan_nama;
            if ($spk->reseller_nama !== null) {
                $nama_pelanggan = "$spk->reseller_nama - $spk->pelanggan_nama";
            }
            $nama_pelanggans->push($nama_pelanggan);
            // END - NAMA PELANGGANS
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
        $label_pelanggans = Pelanggan::label_pelanggans();
        $label_produks = Produk::select('id', 'nama as label', 'nama as value')->get();
        $data = [
            // 'goback' => 'home',
            // 'user_role' => $user_role,
            'menus' => Menu::get(),
            'route_now' => 'home',
            'profile_menus' => Menu::get_profile_menus(),
            'parent_route' => 'home',
            'spk_menus' => Menu::get_spk_menus(),
            'spks' => $spks,
            'nama_pelanggans' => $nama_pelanggans,
            'col_notas' => $col_notas,
            'col_srjalans' => $col_srjalans,
            'col_spk_produks' => $col_spk_produks,
            'col_spk_produk_notas' => $col_spk_produk_notas,
            'col_spk_produk_nota_srjalans' => $col_spk_produk_nota_srjalans,
            'label_pelanggans' => $label_pelanggans,
            'label_produks' => $label_produks,
            // 'user' => Auth::user(),
        ];
        // dump($user_role);
        // dd($spks[7]);
        // dd($col_srjalans[7]);
        // dd($data);
        // dd($col_spk_produk_notas[0]);
        // dd($label_pelanggans[0]);

        $dump = false;
        if ($dump) {
            foreach ($spks as $key => $spk) {
                foreach ($col_notas[$key] as $key2 => $nota) {
                    if (isset($col_spk_produk_nota_srjalans[$key])) {
                        if (isset($col_spk_produk_nota_srjalans[$key][$key2])) {
                        } else {
                            dump($nota->id);
                        }
                    } else {
                        dump($nota->id);
                    }
                }
            }
            dd('cek error');
        }
        return view('app', $data);
    }

    function info() {
        $data = [
            'goback' => 'home',
        ];
        return view('about.index', $data);
    }
}
