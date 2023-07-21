<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spk extends Model
{
    use HasFactory;
    protected $guarded=['id'];

    static function Data_SPK_Nota_Srjalan($spk) {
        // NAMA PELANGGANS
        $nama_pelanggan = $spk->pelanggan_nama;
        if ($spk->reseller_nama !== null) {
            $nama_pelanggan = "$spk->reseller_nama - $spk->pelanggan_nama";
        }
        // END - NAMA PELANGGANS
        $spk_produks = SpkProduk::where('spk_id', $spk->id)->get();
        // dd($spk_produks);
        $spk_notas = SpkNota::where('spk_id', $spk->id)->get();
        $notas = collect();
        $arr_notas = array(); // akan digunakan nanti untuk data tambahan spk_produks di bawah
        $arr_srjalan = array(); // akan digunakan nanti untuk data tambahan spk_produks di bawah
        $cust_kontaks = collect();
        $col_spk_produk_notas = collect();
        $col_srjalans = collect();
        $col_ekspedisi_kontaks = collect();
        $col_col_spk_produk_nota_srjalans = collect();
        foreach ($spk_notas as $spk_nota) {
            // DATA NOTA
            $nota = Nota::find($spk_nota->nota_id);
            $arr_notas[] = $nota->id;
            $spk_produk_notas = SpkProdukNota::where('nota_id', $nota->id)->get();
            $notas->push($nota);
            $col_spk_produk_notas->push($spk_produk_notas);
            // END - DATA NOTA
            // CUST KONTAK
            $cust_kontak = null;
            $json_kontak = null;
            if ($nota->reseller_id !== null) {
                if ($nota->reseller_kontak !== null) {
                    $json_kontak = json_decode($nota->reseller_kontak,true);
                }
            } else {
                if ($nota->cust_kontak !== null) {
                    $json_kontak = json_decode($nota->cust_kontak,true);
                }
            }
            if ($json_kontak !== null) {
                if ($json_kontak['kodearea'] !== null) {
                    $cust_kontak = "($json_kontak[kodearea]) $json_kontak[nomor]";
                } else {
                    $cust_kontak = $json_kontak['nomor'];
                }
            }
            $cust_kontaks->push($cust_kontak);
            // END - CUST KONTAK
            // DATA SJ
            $nota_srjalans = NotaSrjalan::where('nota_id', $nota->id)->get();
            // dump($nota_srjalans);
            $srjalans = collect();
            $ekspedisi_kontaks = collect();
            $col_spk_produk_nota_srjalans = collect();
            foreach ($nota_srjalans as $nota_srjalan) {
                $srjalan = Srjalan::find($nota_srjalan->srjalan_id);
                $srjalans->push($srjalan);
                $arr_srjalan[] = $srjalan->id;
                $spk_produk_nota_srjalans = SpkProdukNotaSrjalan::where('srjalan_id', $srjalan->id)->get();
                $col_spk_produk_nota_srjalans->push($spk_produk_nota_srjalans);
                // EKSPEDISI_KONTAK
                $ekspedisi_kontak = null;
                $json_ekspedisi_kontak = null;
                if ($srjalan->eks_kontak !== null) {
                    $json_ekspedisi_kontak = json_decode($srjalan->eks_kontak, true);
                }
                if ($json_ekspedisi_kontak !== null) {
                    if ($json_ekspedisi_kontak['kodearea'] !== null) {
                        $ekspedisi_kontak = "($json_ekspedisi_kontak[kodearea]) $json_ekspedisi_kontak[nomor]";
                    } else {
                        $ekspedisi_kontak = $json_ekspedisi_kontak['nomor'];
                    }
                }
                $ekspedisi_kontaks->push($ekspedisi_kontak);
                // END - EKSPEDISI_KONTAK
            }
            $col_srjalans->push($srjalans);
            $col_ekspedisi_kontaks->push($ekspedisi_kontaks);
            $col_col_spk_produk_nota_srjalans->push($col_spk_produk_nota_srjalans);
            // END - DATA SJ
        }
        // DATA TAMBAHAN SPK_PRODUKS
        $data_spk_produks = array();
        // dump($arr_notas);
        foreach ($spk_produks as $spk_produk) {
            $spk_produk_notas = SpkProdukNota::where('spk_produk_id', $spk_produk->id)->get();
            $data_nota = array();
            $arr_notas_2 = $arr_notas;
            foreach ($spk_produk_notas as $spk_produk_nota) {
                $data_nota[] = [
                    'nota_id'=>$spk_produk_nota->nota_id,
                    'jumlah'=>$spk_produk_nota->jumlah,
                ];
                // $data_nota->push([
                //     'nota_id'=>$spk_produk_nota->nota_id,
                //     'jumlah'=>$spk_produk_nota->jumlah,
                // ]);
                $arr_notas_2 = array_filter($arr_notas_2, function ($v) use ($spk_produk_nota) {
                   return  $v != $spk_produk_nota->nota_id;
                });
            }
            if (count($arr_notas_2) !== 0) {
                $arr_notas_2 = array_values($arr_notas_2);
                foreach ($arr_notas_2 as $arr_nota) {
                    $data_nota[] = [
                        'nota_id'=>$arr_nota,
                        'jumlah'=>0,
                    ];
                    // $data_nota->push([
                    //     'nota_id'=>$arr_nota,
                    //     'jumlah'=>null,
                    // ]);
                }
            }
            // usort($data_nota, function($a, $b) {return strcmp($a['nota_id'], $b['nota_id']);});
            usort($data_nota, function($a, $b) {return $a['nota_id']<=>$b['nota_id'];});

            // dump($data_nota);
            $spk_produk_nota_srjalans = SpkProdukNotaSrjalan::where('spk_produk_id', $spk_produk->id)->get();
            $data_srjalan = collect();
            foreach ($spk_produk_nota_srjalans as $spk_produk_nota_srjalan) {
                $data_srjalan->push([
                    'srjalan_id'=>$spk_produk_nota_srjalan->srjalan_id,
                    'jumlah'=>$spk_produk_nota_srjalan->jumlah,
                ]);
            }
            $data_spk_produks[] = ['data_nota'=>$data_nota,'data_srjalan'=>$data_srjalan];
            // $data_spk_produks->push(['data_nota'=>$data_nota,'data_srjalan'=>$data_srjalan]);
        }
        // dd($arr_notas);
        // END - DATA TAMBAHAN SPK_PRODUKS

        // DATA TAMBAHAN SPK_PRODUK_NOTAS
        $data_spk_produk_notas = collect();
        // dd($arr_srjalan);
        foreach ($col_spk_produk_notas as $spk_produk_notas) {
            $data_srjalan = collect();
            foreach ($spk_produk_notas as $spk_produk_nota) {
                $spk_produk_nota_srjalans = SpkProdukNotaSrjalan::where('spk_produk_nota_id', $spk_produk_nota->id)->get();
                $data_srjalan_2 = array();
                $arr_srjalan_2 = $arr_srjalan;
                foreach ($spk_produk_nota_srjalans as $spk_produk_nota_srjalan) {
                    $data_srjalan_2[] = [
                        'srjalan_id'=>$spk_produk_nota_srjalan->srjalan_id,
                        'jumlah'=>$spk_produk_nota_srjalan->jumlah,
                    ];
                    $arr_srjalan_2 = array_filter($arr_srjalan_2, function ($v) use ($spk_produk_nota_srjalan) {
                        return  $v != $spk_produk_nota_srjalan->srjalan_id;
                     });
                }
                if (count($arr_srjalan_2) !== 0) {
                    $arr_notas_2 = array_values($arr_notas_2);
                    foreach ($arr_notas_2 as $arr_nota) {
                        $data_srjalan_2[] = [
                            'srjalan_id'=>$arr_nota,
                            'jumlah'=>0,
                        ];
                    }
                }
                usort($data_srjalan_2, function($a, $b) {return $a['srjalan_id']<=>$b['srjalan_id'];});

                $data_srjalan->push($data_srjalan_2);
            }
            $data_spk_produk_notas->push($data_srjalan);
        }
        // END - DATA TAMBAHAN SPK_PRODUK_NOTAS

        $data = [
            'nama_pelanggan' => $nama_pelanggan,
            'notas' => $notas,
            'cust_kontaks' => $cust_kontaks,
            'col_spk_produk_notas' => $col_spk_produk_notas,
            'col_srjalans' => $col_srjalans,
            'col_ekspedisi_kontaks' => $col_ekspedisi_kontaks,
            'col_col_spk_produk_nota_srjalans' => $col_col_spk_produk_nota_srjalans,
            'spk_produks' => $spk_produks,
            'data_spk_produks' => $data_spk_produks,
            'data_spk_produk_notas' => $data_spk_produk_notas,
        ];

        return $data;
    }
}
