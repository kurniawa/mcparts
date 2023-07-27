<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Srjalan extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    static function create_from_spk_produk_nota($spk, $nota, $produk, $spk_produk, $spk_produk_nota, $jumlah) {
        // PELANGGAN
        $alamat_id = null;
        $kontak_id = null;
        $cust_long = null;
        $cust_short = null;
        $cust_kontak = null;
        // RESELLER
        $reseller_alamat_id = null;
        $reseller_kontak_id = null;
        $reseller_long = null;
        $reseller_short = null;
        $reseller_kontak = null;
        // EKSPEDISI
        $ekspedisi_id = null;
        $ekspedisi_nama = null;
        $ekspedisi_alamat_id = null;
        $ekspedisi_kontak_id = null;
        $ekspedisi_long = null;
        $ekspedisi_short = null;
        $ekspedisi_kontak = null;
        // TRANSIT
        $transit_id = null;
        $transit_nama = null;
        $transit_alamat_id = null;
        $transit_kontak_id = null;
        $transit_long = null;
        $transit_short = null;
        $transit_kontak = null;
        $nota_srjalans = NotaSrjalan::where('nota_id',$nota->id)->get();
        $srjalan_acuan = null;
        if (count($nota_srjalans) !== 0) {
            $srjalan_acuan = Srjalan::find($nota_srjalans[0]->srjalan_id);
            // PELANGGAN
            $alamat_id = $srjalan_acuan->alamat_id;
            $kontak_id = $srjalan_acuan->kontak_id;
            $cust_long = $srjalan_acuan->cust_long;
            $cust_short = $srjalan_acuan->cust_short;
            $cust_kontak = $srjalan_acuan->cust_kontak;
            // RESELLER
            $reseller_alamat_id = $srjalan_acuan->reseller_alamat_id;
            $reseller_kontak_id = $srjalan_acuan->reseller_kontak_id;
            $reseller_long = $srjalan_acuan->reseller_long;
            $reseller_short = $srjalan_acuan->reseller_short;
            $reseller_kontak = $srjalan_acuan->reseller_kontak;
            // EKSPEDISI
            $ekspedisi_id = $srjalan_acuan->ekspedisi_id;
            $ekspedisi_nama = $srjalan_acuan->ekspedisi_nama;
            $ekspedisi_alamat_id = $srjalan_acuan->ekspedisi_alamat_id;
            $ekspedisi_kontak_id = $srjalan_acuan->ekspedisi_kontak_id;
            $ekspedisi_long = $srjalan_acuan->ekspedisi_long;
            $ekspedisi_short = $srjalan_acuan->ekspedisi_short;
            $ekspedisi_kontak = $srjalan_acuan->ekspedisi_kontak;
            // TRANSIT
            $transit_id = $srjalan_acuan->transit_id;
            $transit_nama = $srjalan_acuan->transit_nama;
            $transit_alamat_id = $srjalan_acuan->transit_alamat_id;
            $transit_kontak_id = $srjalan_acuan->transit_kontak_id;
            $transit_long = $srjalan_acuan->transit_long;
            $transit_short = $srjalan_acuan->transit_short;
            $transit_kontak = $srjalan_acuan->transit_kontak;
        } else {
            $pelanggan_data = Pelanggan::data($nota->pelanggan_id);
            $alamat_id = $pelanggan_data['alamat_id'];
            $kontak_id = $pelanggan_data['kontak_id'];
            $cust_long = $pelanggan_data['long'];
            $cust_short = $pelanggan_data['short'];
            $cust_kontak = $pelanggan_data['kontak'];
            if ($nota->reseller_id !== null) {
                $reseller_data = Pelanggan::data($nota->reseller_id);
                $reseller_alamat_id = $reseller_data['alamat_id'];
                $reseller_kontak_id = $reseller_data['kontak_id'];
                $reseller_long = $reseller_data['long'];
                $reseller_short = $reseller_data['short'];
                $reseller_kontak = $reseller_data['kontak'];
            }
            $ekspedisi_data = PelangganEkspedisi::data($nota->pelanggan_id, false);
            // EKSPEDISI
            $ekspedisi_id = $ekspedisi_data['ekspedisi_id'];
            $ekspedisi_nama = $ekspedisi_data['ekspedisi_nama'];
            $ekspedisi_alamat_id = $ekspedisi_data['ekspedisi_alamat_id'];
            $ekspedisi_kontak_id = $ekspedisi_data['ekspedisi_kontak_id'];
            $ekspedisi_long = $ekspedisi_data['ekspedisi_long'];
            $ekspedisi_short = $ekspedisi_data['ekspedisi_short'];
            $ekspedisi_kontak = $ekspedisi_data['ekspedisi_kontak'];
            // TRANSIT
            $transit_data = PelangganEkspedisi::data($nota->pelanggan_id, true);
            $transit_id = $transit_data['transit_id'];
            $transit_nama = $transit_data['transit_nama'];
            $transit_alamat_id = $transit_data['transit_alamat_id'];
            $transit_kontak_id = $transit_data['transit_kontak_id'];
            $transit_long = $transit_data['transit_long'];
            $transit_short = $transit_data['transit_short'];
            $transit_kontak = $transit_data['transit_kontak'];
        }
        $user = Auth::user();
        $jumlah_packing = [[
            'tipe_packing' => $produk->tipe_packing,
            'jumlah_packing' => round($jumlah / $produk->aturan_packing),
        ]];
        $jumlah_packing = json_encode($jumlah_packing);
        $srjalan = Srjalan::create([
            // PELANGGAN
            'pelanggan_id'=>$nota->pelanggan_id,
            'pelanggan_nama'=>$nota->pelanggan_nama,
            'alamat_id'=>$alamat_id,
            'kontak_id'=>$kontak_id,
            'cust_long'=>$cust_long,
            'cust_short'=>$cust_short,
            'cust_kontak'=>$cust_kontak,
            // RESELLER
            'reseller_id'=>$nota->reseller_id,
            'reseller_nama'=>$nota->reseller_nama,
            'reseller_alamat_id'=>$reseller_alamat_id,
            'reseller_kontak_id'=>$reseller_kontak_id,
            'reseller_long'=>$reseller_long,
            'reseller_short'=>$reseller_short,
            'reseller_kontak'=>$reseller_kontak,
            // EKSPEDISI
            'ekspedisi_id' => $ekspedisi_id,
            'ekspedisi_nama' => $ekspedisi_nama,
            'ekspedisi_alamat_id' => $ekspedisi_alamat_id,
            'ekspedisi_kontak_id' => $ekspedisi_kontak_id,
            'ekspedisi_long' => $ekspedisi_long,
            'ekspedisi_short' => $ekspedisi_short,
            'ekspedisi_kontak' => $ekspedisi_kontak,
            // TRANSIT
            'transit_id' => $transit_id,
            'transit_nama' => $transit_nama,
            'transit_alamat_id' => $transit_alamat_id,
            'transit_kontak_id' => $transit_kontak_id,
            'transit_long' => $transit_long,
            'transit_short' => $transit_short,
            'transit_kontak' => $transit_kontak,
            //
            'jumlah_packing'=>$jumlah_packing,
            'created_by'=>$user->username,
            'updated_by'=>$user->username,
        ]);
        // UPDATE NO_SRJALAN
        $srjalan->no_srjalan = "SJ-$srjalan->id";
        $srjalan->save();
        // CREATE NOTA_SRJALAN
        $nota_srjalan = NotaSrjalan::create([
            'nota_id' => $nota->id,
            'srjalan_id' => $srjalan->id,
        ]);
        // CREATE SPK_PRODUK_NOTA_SRJALAN
        $spk_produk_nota_srjalan = SpkProdukNotaSrjalan::create([
            'spk_id' => $spk->id,
            'produk_id' => $spk_produk->produk_id,
            'nota_id' => $nota->id,
            'srjalan_id' => $srjalan->id,
            'spk_produk_id' => $spk_produk->id,
            'spk_produk_nota_id' => $spk_produk_nota->id,
            'tipe_packing' => $produk->tipe_packing,
            'jumlah' => $jumlah,
            'jumlah_packing' => round($jumlah / $produk->aturan_packing),
        ]);

        return $srjalan;
    }

    static function update_jumlah_packing_srjalan($srjalan) {
        $user = Auth::user();
        $spk_produk_nota_srjalans = SpkProdukNotaSrjalan::where('srjalan_id', $srjalan->id)->get();
        $jumlah_packing = array();
        $tipe_packings = TipePacking::all();
        foreach ($spk_produk_nota_srjalans as $spk_produk_nota_srjalan) {
            if (count($jumlah_packing) === 0) {
                $jumlah_packing[] = ["tipe_packing"=>$spk_produk_nota_srjalan->tipe_packing, "jumlah"=>$spk_produk_nota_srjalan->jumlah, "jumlah_packing"=>$spk_produk_nota_srjalan->jumlah_packing];
            } else {
                $is_tipe_packing_exist = false;
                foreach ($jumlah_packing as $jml_packing) {
                    foreach ($tipe_packings as $tipe_packing) {
                        if ($tipe_packing->name === $spk_produk_nota_srjalan->tipe_packing) {
                            if ($jml_packing['tipe_packing'] === $spk_produk_nota_srjalan->tipe_packing) {
                                $jml_packing['jumlah'] += $spk_produk_nota_srjalan->jumlah;
                                $jml_packing['jumlah_packing'] += $spk_produk_nota_srjalan->jumlah_packing;
                                $is_tipe_packing_exist = true;
                            }
                        }
                    }
                }
                if (!$is_tipe_packing_exist) {
                    $jumlah_packing[] = ["tipe_packing"=>$spk_produk_nota_srjalan->tipe_packing, "jumlah"=>$spk_produk_nota_srjalan->jumlah, "jumlah_packing"=>$spk_produk_nota_srjalan->jumlah_packing];
                }
            }
        }
        if (count($jumlah_packing) === 0) {
            $jumlah_packing = null;
        } else {
            $jumlah_packing = json_encode($jumlah_packing);
        }
        // dump($srjalan);
        // dd($jumlah_packing);
        $srjalan->jumlah_packing = $jumlah_packing;
        $srjalan->updated_by = $user->username;
        $srjalan->save();
    }

    static function kaji_ulang_spk_dan_spk_produk($spk) {
        $spk_produks = SpkProduk::where('spk_id', $spk->id)->get();
        $jumlah_sudah_srjalan_gabungan = 0;
        foreach ($spk_produks as $spk_produk) {
            $spk_produk_nota_srjalans = SpkProdukNotaSrjalan::where('spk_produk_id', $spk_produk->id)->get();
            $jumlah_sudah_srjalan = 0;
            foreach ($spk_produk_nota_srjalans as $spk_produk_nota_srjalan) {
                $jumlah_sudah_srjalan += $spk_produk_nota_srjalan->jumlah;
            }
            $spk_produk->jumlah_sudah_srjalan = $jumlah_sudah_srjalan;
            $spk_produk->save();

            $jumlah_sudah_srjalan_gabungan += $jumlah_sudah_srjalan;
        }

        $status_srjalan = 'BELUM';
        if ($spk->jumlah_total === $jumlah_sudah_srjalan_gabungan) {
            $status_srjalan = 'SEMUA';
        } elseif ($jumlah_sudah_srjalan_gabungan > 0) {
            $status_srjalan = 'SEBAGIAN';
        } elseif ($jumlah_sudah_srjalan_gabungan <= 0) {
            $status_srjalan = 'BELUM';
        }

        $spk->jumlah_sudah_srjalan = $jumlah_sudah_srjalan_gabungan;
        $spk->status_srjalan = $status_srjalan;
        $spk->save();
    }
}
