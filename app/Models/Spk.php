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
                if ($srjalan->ekspedisi_kontak !== null) {
                    $json_ekspedisi_kontak = json_decode($srjalan->ekspedisi_kontak, true);
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
                // dump($spk_produk_nota->nota_id);
                $arr_notas_2 = array_filter($arr_notas_2, function ($v) use ($spk_produk_nota) {
                   return  $v != $spk_produk_nota->nota_id;
                });
                // dump($arr_notas);
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
        // dump($arr_srjalan);
        // 1. Surat Jalan mana saja yang terkait dengan spk?
        $srjalan_ids = array();
        foreach ($spk_notas as $spk_nota) {
            $nota_srjalans = NotaSrjalan::where('nota_id', $spk_nota->nota_id)->get();
            foreach ($nota_srjalans as $nota_srjalan) {
                $srjalan_ids[] = $nota_srjalan->srjalan_id;
            }
        }
        $srjalan_ids = array_unique($srjalan_ids); // filter duplicate
        usort($srjalan_ids, function($a, $b) {return $a<=>$b;}); // sort dari yang id kecil

        $data_spk_produk_notas = collect();
        // dump($col_spk_produk_notas);
        foreach ($col_spk_produk_notas as $spk_produk_notas) {
            $data_spk_produk_notas_2 = collect();
            foreach ($spk_produk_notas as $spk_produk_nota) {
                $spk_produk_nota_srjalans = SpkProdukNotaSrjalan::where('spk_produk_nota_id', $spk_produk_nota->id)->get();
                $data_spk_produk_notas_3 = collect();
                if (count($spk_produk_nota_srjalans) !== 0) {
                    // foreach ($spk_produk_nota_srjalans as $spk_produk_nota_srjalan) {
                    //     foreach ($srjalan_ids as $srjalan_id) {
                    //         if ($data_spk_produk_notas_3->contains('srjalan_id', $srjalan_id)) {
                    //             if ($spk_produk_nota_srjalan->jumlah !== 0 && $spk_produk_nota_srjalan->jumlah !== null) {
                    //                 foreach ($data_spk_produk_notas_3 as $data_spk_produk_nota) {
                    //                     if ($data_spk_produk_nota['srjalan_id'] === $srjalan_id) {
                    //                         $data_spk_produk_notas_3->push([
                    //                             'srjalan_id' => $srjalan_id,
                    //                             'jumlah' => $spk_produk_nota_srjalan->jumlah,
                    //                         ]);
                    //                     }
                    //                 }
                    //             }
                    //         } else {
                    //             if ($spk_produk_nota_srjalan->srjalan_id === $srjalan_id) {
                    //                 $data_spk_produk_notas_3->push([
                    //                     'srjalan_id' => $srjalan_id,
                    //                     'jumlah' => $spk_produk_nota_srjalan->jumlah,
                    //                 ]);
                    //             } else {
                    //                 $data_spk_produk_notas_3->push([
                    //                     'srjalan_id' => $srjalan_id,
                    //                     'jumlah' => 0,
                    //                 ]);
                    //             }
                    //         }
                    //     }
                    // }
                    // dump($srjalan_ids);
                    $srjalan_ids_2 = $srjalan_ids;
                    foreach ($spk_produk_nota_srjalans as $spk_produk_nota_srjalan) {
                        foreach ($srjalan_ids as $srjalan_id) {
                            if ($spk_produk_nota_srjalan->srjalan_id === $srjalan_id) {
                                $data_spk_produk_notas_3->push([
                                    'srjalan_id' => $srjalan_id,
                                    'jumlah' => $spk_produk_nota_srjalan->jumlah,
                                ]);
                                $srjalan_ids_2 = array_diff($srjalan_ids_2, array($srjalan_id));
                                $srjalan_ids_2 = array_values($srjalan_ids_2);
                                // dump($spk_produk_nota->id, $srjalan_ids_2);
                            }
                        }
                        // dump($srjalan_ids_2);
                    }
                    foreach ($srjalan_ids_2 as $srjalan_id) {
                        $data_spk_produk_notas_3->push([
                            'srjalan_id' => $srjalan_id,
                            'jumlah' => 0,
                        ]);
                    }
                } else { // ketika nota item belum memiliki srjalan sama sekali
                    foreach ($srjalan_ids as $srjalan_id) {
                        $data_spk_produk_notas_3->push([
                            'srjalan_id' => $srjalan_id,
                            'jumlah' => 0,
                        ]);
                    }
                }
                $data_spk_produk_notas_2->push($data_spk_produk_notas_3);
            }
            $data_spk_produk_notas->push($data_spk_produk_notas_2);
        }
        // dd($data_spk_produk_notas);

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
        // dd($pilihan_srjalans);
        return $data;
    }

    static function update_data_SPK($spk) { // UPDATE jumlah_selesai, jumlah_total
        $spk_produks = SpkProduk::where('spk_id', $spk->id)->get();
        // dd($spk_produks);
        $jumlah_selesai = 0;
        $jumlah_total = 0;
        foreach ($spk_produks as $spk_produk) {
            $jumlah_selesai += $spk_produk->jumlah_selesai;
            $jumlah_total += $spk_produk->jumlah_total;
        }
        $spk->jumlah_selesai = $jumlah_selesai;
        $spk->jumlah_total = $jumlah_total;
        $spk->save();
    }

    static function user($username) {
        $user = User::where('username', $username)->first();
        return $user;
    }

    function notas() {
        return $this->belongsToMany(Nota::class, 'spk_notas', 'spk_id', 'nota_id');
    }
}
