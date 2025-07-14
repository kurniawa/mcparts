<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Nota;
use Illuminate\Http\Request;

class AccountingController2 extends Controller
{
    function laba_rugi(Request $request)
    {
        $get = $request->query();
        // Set di awal tanpa filter -> tanggal bulan ini


        $notas = collect();

        if (count($get) > 0) {
            // dd($get);
            $date_start = null;
            $date_end = null;

            if ($get['from_day'] && $get['from_month'] && $get['from_year'] && $get['to_day'] && $get['to_month'] && $get['to_year']) {
                $date_start = "$get[from_year]-$get[from_month]-$get[from_day]";
                $date_end = "$get[to_year]-$get[to_month]-$get[to_day] 23:59:59";
            }

            if ($date_start && $date_end) {
                $notas = Nota::whereBetween('created_at', [$date_start, $date_end])->get();
            } else {
                $request->validate(['error'=>'required'],['error.required'=>'customer || time_range']);
            }
        } else {
            $date_start = date('Y') . "-" . date('m') . "-01";
            $date_end = date('Y') . "-" . date('m') . "-" . date('d') . " 23:59:59";
            $notas = Nota::whereBetween('created_at', [$date_start, $date_end])->get();
        }

        $penjualan_barang_dan_jasa = 0;
        foreach ($notas as $nota) {
            $penjualan_barang_dan_jasa += $nota->harga_total;
        }


        $data = [
            'menus' => Menu::get(),
            'route_now' => 'accounting.laba_rugi',
            'parent_route' => 'accounting.index',
            'profile_menus' => Menu::get_profile_menus(),
            'accounting_menus' => Menu::get_accounting_menus(),
            'penjualan_barang_dan_jasa' => $penjualan_barang_dan_jasa,
        ];
        return view('accounting.laba_rugi', $data);
    }
}
