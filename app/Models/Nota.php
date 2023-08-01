<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Nota extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    static function create_from_spk_produk($spk, $spk_produk, $jumlah_total) {
        $alamat_id = null;
        $kontak_id = null;
        $cust_long = null;
        $cust_short = null;
        $cust_kontak = null;
        $reseller_alamat_id = null;
        $reseller_kontak_id = null;
        $reseller_long = null;
        $reseller_short = null;
        $reseller_kontak = null;
        $spk_notas = SpkNota::where('spk_id',$spk->id)->get();
        $nota_acuan = null;
        if (count($spk_notas) !== 0) {
            $nota_acuan = Nota::find($spk_notas[0]->nota_id);
            $alamat_id = $nota_acuan->alamat_id;
            $kontak_id = $nota_acuan->kontak_id;
            $cust_long = $nota_acuan->cust_long;
            $cust_short = $nota_acuan->cust_short;
            $cust_kontak = $nota_acuan->cust_kontak;
            $reseller_alamat_id = $nota_acuan->reseller_alamat_id;
            $reseller_kontak_id = $nota_acuan->reseller_kontak_id;
            $reseller_long = $nota_acuan->reseller_long;
            $reseller_short = $nota_acuan->reseller_short;
            $reseller_kontak = $nota_acuan->reseller_kontak;
        } else {
            $pelanggan_data = Pelanggan::data($spk->pelanggan_id);
            $alamat_id = $pelanggan_data['alamat_id'];
            $kontak_id = $pelanggan_data['kontak_id'];
            $cust_long = $pelanggan_data['long'];
            $cust_short = $pelanggan_data['short'];
            $cust_kontak = $pelanggan_data['kontak'];
            if ($spk->reseller_id !== null) {
                $reseller_data = Pelanggan::data($spk->reseller_id);
                $reseller_alamat_id = $reseller_data['alamat_id'];
                $reseller_kontak_id = $reseller_data['kontak_id'];
                $reseller_long = $reseller_data['long'];
                $reseller_short = $reseller_data['short'];
                $reseller_kontak = $reseller_data['kontak'];
            }
        }
        $produk = Produk::find($spk_produk->produk_id);
        $harga_produk = Produk::get_harga_pelanggan($produk->id, $spk->pelanggan_id);
        // dump($harga_produk);
        // $harga_total =
        $user = Auth::user();
        $nota = Nota::create([
            'pelanggan_id'=>$spk->pelanggan_id,
            'reseller_id'=>$spk->reseller_id,
            'pelanggan_nama'=>$spk->pelanggan_nama,
            'reseller_nama'=>$spk->reseller_nama,
            'jumlah_total'=>$jumlah_total,
            'harga_total'=>$harga_produk * $jumlah_total,
            //
            'alamat_id'=>$alamat_id,
            'reseller_alamat_id'=>$reseller_alamat_id,
            'kontak_id'=>$kontak_id,
            'reseller_kontak_id'=>$reseller_kontak_id,
            'cust_long'=>$cust_long,
            'cust_short'=>$cust_short,
            'cust_kontak'=>$cust_kontak,
            'reseller_long'=>$reseller_long,
            'reseller_short'=>$reseller_short,
            'reseller_kontak'=>$reseller_kontak,
            'created_by'=>$user->username,
            'updated_by'=>$user->username,
        ]);
        // UPDATE NO_NOTA
        $nota->no_nota = "N-$nota->id";
        $nota->save();
        // CREATE SPK_NOTA
        $spk_nota = SpkNota::create([
            'spk_id' => $spk->id,
            'nota_id' => $nota->id,
        ]);
        // CREATE SPK_PRODUK_NOTA

        $spk_produk_nota = SpkProdukNota::create([
            'spk_id'=>$spk->id,
            'produk_id'=>$spk_produk->produk_id,
            'spk_produk_id'=>$spk_produk->id,
            'nota_id'=>$nota->id,
            'jumlah'=>$jumlah_total,
            'nama_nota'=>$produk->nama_nota,
            'harga'=>$harga_produk,
            'harga_t'=>$harga_produk * $jumlah_total,
        ]);
    }

    static function kaji_ulang_spk_dan_spk_produk($spk) {
        $spk_produks = SpkProduk::where('spk_id', $spk->id)->get();
        $jumlah_sudah_nota_gabungan = 0;
        foreach ($spk_produks as $spk_produk) {
            $spk_produk_notas = SpkProdukNota::where('spk_produk_id', $spk_produk->id)->get();
            $jumlah_sudah_nota = 0;
            foreach ($spk_produk_notas as $spk_produk_nota) {
                $jumlah_sudah_nota += $spk_produk_nota->jumlah;
            }
            $spk_produk->jumlah_sudah_nota = $jumlah_sudah_nota;
            $spk_produk->save();

            $jumlah_sudah_nota_gabungan += $jumlah_sudah_nota;
        }

        $status_nota = 'BELUM';
        if ($spk->jumlah_total === $jumlah_sudah_nota_gabungan) {
            $status_nota = 'SEMUA';
        } elseif ($jumlah_sudah_nota_gabungan > 0) {
            $status_nota = 'SEBAGIAN';
        } elseif ($jumlah_sudah_nota_gabungan <= 0) {
            $status_nota = 'BELUM';
        }

        $spk->status_nota = $status_nota;
        $spk->jumlah_sudah_nota = $jumlah_sudah_nota_gabungan;
        $spk->save();
    }

    static function update_data_nota_srjalan($spk) {
        $spk_notas = SpkNota::where('spk_id', $spk->id)->get();
        foreach ($spk_notas as $spk_nota) {
            $nota = Nota::find($spk_nota->nota_id);
            $spk_produk_notas = SpkProdukNota::where('nota_id', $nota->id)->get();
            $harga_total = 0;
            $jumlah_total = 0;
            foreach ($spk_produk_notas as $spk_produk_nota) {
                $harga_total += $spk_produk_nota->harga_t;
                $jumlah_total += $spk_produk_nota->jumlah;
            }
            $nota->jumlah_total = $jumlah_total;
            $nota->harga_total = $harga_total;
            $nota->save();

            $nota_srjalans = NotaSrjalan::where('nota_id', $nota->id)->get();
            foreach ($nota_srjalans as $nota_srjalan) {
                $srjalan = Srjalan::find($nota_srjalan->srjalan_id);
                Srjalan::update_jumlah_packing_srjalan($srjalan);
            }
        }
    }
}
