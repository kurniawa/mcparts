<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Nota;
use App\Models\Pelanggan;
use App\Models\SpkProdukNota;
use Illuminate\Http\Request;

class PenjualanController extends Controller
{
    function index(Request $request) {
        $get = $request->query();
        // Set di awal tanpa filter -> tanggal bulan ini


        $notas = collect();

        $pelanggan_id = null;
        if (count($get) > 0) {
            // dd($get);
            $date_start = null;
            $date_end = null;

            if ($get['from_day'] && $get['from_month'] && $get['from_year'] && $get['to_day'] && $get['to_month'] && $get['to_year']) {
                $date_start = "$get[from_year]-$get[from_month]-$get[from_day]";
                $date_end = "$get[to_year]-$get[to_month]-$get[to_day] 23:59:59";
            }

            if ($get['pelanggan_nama'] && $date_start && $date_end) {
                $notas = Nota::whereBetween('created_at', [$date_start, $date_end])->where('pelanggan_nama', 'like', "%$get[pelanggan_nama]%")->orderBy('pelanggan_nama')->orderBy('created_at')->get();
                $pelanggan = Pelanggan::where('nama', $get['pelanggan_nama'])->first();
                $pelanggan_id = $pelanggan->id;
            } elseif ($get['pelanggan_nama'] && !$date_start && !$date_end) {
                $notas = Nota::where('pelanggan_nama', 'like', "%$get[pelanggan_nama]%")->orderBy('created_at')->get();
                $notas_orderby_date = Nota::where('pelanggan_nama', 'like', "%$get[pelanggan_nama]%")->orderBy('created_at')->get();
                $date_start = $notas_orderby_date[0]->created_at;
                $date_start = date('Y-m-d', strtotime($date_start));
                $date_end = $notas_orderby_date[count($notas_orderby_date) - 1]->created_at;
                $date_end = date('Y-m-d', strtotime($date_end)) . " 23:59:59";
                $pelanggan = Pelanggan::where('nama', $get['pelanggan_nama'])->first();
                $pelanggan_id = $pelanggan->id;
            } elseif (!$get['pelanggan_nama'] && $date_start && $date_end) {
                $notas = Nota::whereBetween('created_at', [$date_start, $date_end])->orderBy('pelanggan_nama')->orderBy('created_at')->get();
            } else {
                $request->validate(['error'=>'required'],['error.required'=>'customer || time_range']);
            }
        } else {
            $date_start = date('Y') . "-" . date('m') . "-01";
            $date_end = date('Y') . "-" . date('m') . "-" . date('d') . " 23:59:59";
            $notas = Nota::whereBetween('created_at', [$date_start, $date_end])->orderBy('pelanggan_nama')->orderBy('created_at')->get();
        }

        // dump($notas);
        // dd($notas->groupBy('pelanggan_nama'));
        // dd($date_start, $date_end);
        $notas_grouped_pelanggan = $notas->groupBy('pelanggan_nama');
        $total_penjualan_pelanggan_all = collect();
        $nota_subtotal_all = collect();
        $nota_detail_items_all = collect();
        $spk_produk_nota_pelanggans = collect();
        $grand_total = 0;
        $key_class = 0;
        foreach ($notas_grouped_pelanggan as $notas_grouped) {
            $class = 'bg-sky-100';
            if ($key_class === 1) {
                $class = 'bg-orange-100';
            }
            $key_class++;
            if ($key_class > 1) {
                $key_class = 0;
            }
            $total_penjualan = 0;
            foreach ($notas_grouped as $key_nota => $nota) {
                $total_penjualan += $nota->harga_total;
                if ($key_nota === count($notas_grouped) - 1) {
                    $nota_subtotal_all->push([
                        'created_at' => $nota->created_at,
                        'no_nota' => $nota->no_nota,
                        'pelanggan_nama' => $nota->pelanggan_nama,
                        'harga_total' => $nota->harga_total,
                        'subtotal' => $total_penjualan,
                        'class' => $class,
                    ]);
                } else {
                    $nota_subtotal_all->push([
                        'created_at' => $nota->created_at,
                        'no_nota' => $nota->no_nota,
                        'pelanggan_nama' => $nota->pelanggan_nama,
                        'harga_total' => $nota->harga_total,
                        'subtotal' => null,
                        'class' => $class,
                    ]);
                }
                $spk_produk_notas = SpkProdukNota::where('nota_id', $nota->id)->get();
                foreach ($spk_produk_notas as $spk_produk_nota) {
                    $nota_detail_items_all->push([
                        'created_at' => $nota->created_at,
                        'no_nota' => $nota->no_nota,
                        'pelanggan_nama' => $nota->pelanggan_nama,
                        'cust_short' => $nota->cust_short,
                        'nama_nota' => $spk_produk_nota->nama_nota,
                        'jumlah' => $spk_produk_nota->jumlah,
                        'harga' => $spk_produk_nota->harga,
                        'harga_t' => $spk_produk_nota->harga_t,
                        'class' => $class,
                    ]);

                    if ($pelanggan_id) {
                        $spk_produk_nota_pelanggans->push($spk_produk_nota);
                    }

                }
            }
            $total_penjualan_pelanggan_all->push([
                'pelanggan_nama' => $notas_grouped[0]->pelanggan_nama,
                'total_penjualan' => $total_penjualan,
            ]);
            $grand_total += $total_penjualan;
        }

        // DATA ITEM YANG BIASA DIBELI OLEH PELANGGAN
        $item_pelanggans = null;
        if ($pelanggan_id) {
            $item_pelanggans = $spk_produk_nota_pelanggans->groupBy('nama_nota');
        }
        // END - DATA ITEM YANG BIASA DIBELI OLEH PELANGGAN
        // dd($item_pelanggans);
        $data = [
            'menus' => Menu::get(),
            'route_now' => 'penjualans.index',
            'profile_menus' => Menu::get_profile_menus(),
            'total_penjualan_pelanggan_all' => $total_penjualan_pelanggan_all,
            'nota_subtotal_all' => $nota_subtotal_all,
            'nota_detail_items_all' => $nota_detail_items_all,
            'grand_total' => $grand_total,
            'item_pelanggans' => $item_pelanggans,
        ];
        // dd($nota_detail_items_all);
        // dd($data);
        return view('penjualans.index', $data);
    }
}
