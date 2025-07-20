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
        $page_to_show = 'index';

        $pelanggan_id = null;
        if (count($get) > 0) {
            // dd($get);
            if (isset($get['page_to_show'])) {
                $page_to_show = $get['page_to_show'];
            }
            $date_start = null;
            $date_end = null;

            if ($get['from_day'] && $get['from_month'] && $get['from_year'] && $get['to_day'] && $get['to_month'] && $get['to_year']) {
                $date_start = "$get[from_year]-$get[from_month]-$get[from_day]";
                $date_end = "$get[to_year]-$get[to_month]-$get[to_day] 23:59:59";
            }

            if ($get['pelanggan_nama'] && $date_start && $date_end) {
                $notas = Nota::whereBetween('created_at', [$date_start, $date_end])->where('pelanggan_nama', 'like', "%$get[pelanggan_nama]%")->orderBy('pelanggan_nama')->orderBy('created_at')->get();
                $pelanggan = Pelanggan::where('nama', 'like', "%$get[pelanggan_nama]%")->first();
                $pelanggan_id = $pelanggan->id;
            } elseif ($get['pelanggan_nama'] && !$date_start && !$date_end) {
                $pelanggans = Pelanggan::where('nama', 'like', "%$get[pelanggan_nama]%")->get();
                $notas = collect();
                foreach ($pelanggans as $key => $pelanggan) {
                    $notas = $notas->merge(Nota::where('pelanggan_nama', $pelanggan->nama)->latest()->limit(300)->get()->sortBy('created_at'));
                }
                // $notas_orderby_date = $notas->sortBy('created_at');
                // $date_start = $notas_orderby_date[0]->created_at;
                // $date_start = date('Y-m-d', strtotime($date_start));
                // $date_end = $notas_orderby_date[count($notas_orderby_date) - 1]->created_at;
                // $date_end = date('Y-m-d', strtotime($date_end)) . " 23:59:59";
                // $pelanggan = Pelanggan::where('nama', $get['pelanggan_nama'])->first();
                // $pelanggan_id = $pelanggan->id;
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
        $totalPenjualanPelangganAll = [];
        $notaSubtotalAll = [];
        $notaDetailItemsAll = [];
        $spk_produk_nota_pelanggans = collect();
        $grandTotal = 0;

        // Untuk component PiutangPenjualan
        $spk_produk_nota_pelanggans_for_piutang = collect();
        $totalPenjualanPelangganAllForPiutang = [];
        $notaSubtotalAllForPiutang = [];
        $notaDetailItemsAllForPiutang = [];
        $grandTotalForPiutang = 0;

        foreach ($notas_grouped_pelanggan as $notas_grouped) {
            // $class = 'bg-sky-100';
            // $class = 'bg-red-100';
            $total_penjualan = 0;
            $total_penjualan_for_piutang = 0;
            foreach ($notas_grouped as $key_nota => $nota) {
                $class = 'bg-red-200';
                if ($nota->status_bayar === 'lunas') {
                    $class = 'bg-green-200';
                } elseif ($nota->status_bayar === 'sebagian') {
                    $class = 'bg-orange-200';
                }
                $total_penjualan += $nota->harga_total;
                if ($key_nota === count($notas_grouped) - 1) {
                    $notaSubtotalAll[] = [
                        'created_at' => $nota->created_at,
                        'no_nota' => $nota->no_nota,
                        'pelanggan_nama' => $nota->pelanggan_nama,
                        'harga_total' => $nota->harga_total,
                        'subtotal' => $total_penjualan,
                        'class' => $class,
                    ];
                    if ($nota->status_bayar !== 'lunas') {
                        $notaSubtotalAllForPiutang[] = [
                            'created_at' => $nota->created_at,
                            'no_nota' => $nota->no_nota,
                            'pelanggan_nama' => $nota->pelanggan_nama,
                            'harga_total' => $nota->harga_total,
                            'subtotal' => $total_penjualan,
                            'class' => $class,
                        ];
                        $total_penjualan_for_piutang += $nota->harga_total;
                    }
                } else {
                    $notaSubtotalAll[] = [
                        'created_at' => $nota->created_at,
                        'no_nota' => $nota->no_nota,
                        'pelanggan_nama' => $nota->pelanggan_nama,
                        'harga_total' => $nota->harga_total,
                        'subtotal' => null,
                        'class' => $class,
                    ];
                    if ($nota->status_bayar !== 'lunas') {
                        $notaSubtotalAllForPiutang[] = [
                            'created_at' => $nota->created_at,
                            'no_nota' => $nota->no_nota,
                            'pelanggan_nama' => $nota->pelanggan_nama,
                            'harga_total' => $nota->harga_total,
                            'subtotal' => $total_penjualan,
                            'class' => $class,
                        ];
                        $total_penjualan_for_piutang += $nota->harga_total;
                    }
                }
                $spk_produk_notas = SpkProdukNota::where('nota_id', $nota->id)->get();
                $spk_produk_notas_for_piutang = [];
                if ($nota->status_bayar !== 'lunas') {
                    $spk_produk_notas_for_piutang = SpkProdukNota::where('nota_id', $nota->id)->get();
                }
                foreach ($spk_produk_notas as $spk_produk_nota) {
                    $notaDetailItemsAll[] = [
                        'created_at' => $nota->created_at,
                        'no_nota' => $nota->no_nota,
                        'pelanggan_nama' => $nota->pelanggan_nama,
                        'cust_short' => $nota->cust_short,
                        'nama_nota' => $spk_produk_nota->nama_nota,
                        'jumlah' => $spk_produk_nota->jumlah,
                        'harga' => $spk_produk_nota->harga,
                        'harga_t' => $spk_produk_nota->harga_t,
                        'class' => $class,
                    ];

                    if ($pelanggan_id) {
                        $spk_produk_nota_pelanggans->push($spk_produk_nota);
                    }

                }

                foreach ($spk_produk_notas_for_piutang as $spk_produk_nota) {
                    $notaDetailItemsAllForPiutang[] = [
                        'created_at' => $nota->created_at,
                        'no_nota' => $nota->no_nota,
                        'pelanggan_nama' => $nota->pelanggan_nama,
                        'cust_short' => $nota->cust_short,
                        'nama_nota' => $spk_produk_nota->nama_nota,
                        'jumlah' => $spk_produk_nota->jumlah,
                        'harga' => $spk_produk_nota->harga,
                        'harga_t' => $spk_produk_nota->harga_t,
                        'class' => $class,
                    ];

                    if ($pelanggan_id) {
                        $spk_produk_nota_pelanggans_for_piutang->push($spk_produk_nota);
                    }
                }
            }
            $totalPenjualanPelangganAll[] = [
                'pelanggan_nama' => $notas_grouped[0]->pelanggan_nama,
                'total_penjualan' => $total_penjualan,
            ];
            $grandTotal += $total_penjualan;
            if ($total_penjualan_for_piutang > 0) {
                $totalPenjualanPelangganAllForPiutang[] = [
                    'pelanggan_nama' => $notas_grouped[0]->pelanggan_nama,
                    'total_penjualan' => $total_penjualan_for_piutang,
                ];
                $grandTotalForPiutang += $total_penjualan_for_piutang;
            }
        }

        // DATA ITEM YANG BIASA DIBELI OLEH PELANGGAN
        $itemPelanggans = null;
        if ($pelanggan_id) {
            $itemPelanggans = $spk_produk_nota_pelanggans->groupBy('nama_nota');
        }
        // END - DATA ITEM YANG BIASA DIBELI OLEH PELANGGAN
        // dd($itemPelanggans);
        $data = [
            'menus' => Menu::get(),
            'route_now' => 'penjualans.index',
            'profile_menus' => Menu::get_profile_menus(),
            'totalPenjualanPelangganAll' => $totalPenjualanPelangganAll,
            'notaSubtotalAll' => $notaSubtotalAll,
            'notaDetailItemsAll' => $notaDetailItemsAll,
            'grandTotal' => $grandTotal,
            'itemPelanggans' => $itemPelanggans,
            'page_to_show' => $page_to_show,
            'totalPenjualanPelangganAllForPiutang' => $totalPenjualanPelangganAllForPiutang,
            'notaSubtotalAllForPiutang' => $notaSubtotalAllForPiutang,
            'notaDetailItemsAllForPiutang' => $notaDetailItemsAllForPiutang,
            'grandTotalForPiutang' => $grandTotalForPiutang,
        ];
        // dd($notaDetailItemsAll);
        // dd($data);
        return view('penjualans.index', $data);
    }
}
