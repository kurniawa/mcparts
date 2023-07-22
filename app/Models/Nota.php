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
        $user = Auth::user();
        $nota = Nota::create([
            'pelanggan_id'=>$spk->pelanggan_id,
            'reseller_id'=>$spk->reseller_id,
            'pelanggan_nama'=>$spk->pelanggan_nama,
            'reseller_nama'=>$spk->reseller_nama,
            'jumlah_total'=>$jumlah_total,
            'harga_total'=>$spk_produk->harga * $jumlah_total,
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
        $produk = Produk::find($spk_produk->produk_id);
        $spk_produk_nota = SpkProdukNota::create([
            'spk_id'=>$spk->id,
            'produk_id'=>$spk_produk->produk_id,
            'spk_produk_id'=>$spk_produk->id,
            'nota_id'=>$nota->id,
            'jumlah'=>$jumlah_total,
            'nama_nota'=>$produk->nama_nota,
            'harga'=>$spk_produk->harga,
            'harga_t'=>$spk_produk->harga * $jumlah_total,
        ]);
    }
}
