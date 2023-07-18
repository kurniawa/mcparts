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
        $spk_notas = SpkNota::where('spk_id', $spk->id)->get();
        $notas = collect();
        $cust_kontaks = collect();
        $col_spk_produk_notas = collect();
        $col_srjalans = collect();
        $col_ekspedisi_kontaks = collect();
        $col_col_spk_produk_nota_srjalans = collect();
        foreach ($spk_notas as $spk_nota) {
            // DATA NOTA
            $nota = Nota::find($spk_nota->nota_id);
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

        $data = [
            'nama_pelanggan' => $nama_pelanggan,
            'spk_produks' => $spk_produks,
            'notas' => $notas,
            'cust_kontaks' => $cust_kontaks,
            'col_spk_produk_notas' => $col_spk_produk_notas,
            'col_srjalans' => $col_srjalans,
            'col_ekspedisi_kontaks' => $col_ekspedisi_kontaks,
            'col_col_spk_produk_nota_srjalans' => $col_col_spk_produk_nota_srjalans,
        ];

        return $data;
    }
}
