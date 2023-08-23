<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    static function get_harga_pelanggan($produk_id, $pelanggan_id) {
        $pelanggan_produk = PelangganProduk::where('pelanggan_id', $pelanggan_id)->where('produk_id', $produk_id)->where('status', 'DEFAULT')->first();
        $produk_harga = ProdukHarga::where('produk_id', $produk_id)->where('status', 'DEFAULT')->first();
        $harga_produk = $produk_harga->harga;
        if ($pelanggan_produk !== null) {
            $harga_produk = $pelanggan_produk->harga_khusus;
        } else {
            $pelanggan_produk = PelangganProduk::where('pelanggan_id', $pelanggan_id)->where('produk_id', $produk_id)->latest()->first();
            if ($pelanggan_produk !== null) {
                $harga_produk = $pelanggan_produk->harga_khusus;
            }
        }

        return $harga_produk;
    }

    static function get_types() {
        return [
            ['tipe'=>'SJ-Variasi', 'initial'=>'var'],
            ['tipe'=>'SJ-Kombinasi', 'initial'=>'kom'],
            ['tipe'=>'SJ-Motif', 'initial'=>'mot'],
            ['tipe'=>'SJ-T.Sixpack', 'initial'=>'t-sp'],
            ['tipe'=>'SJ-Japstyle', 'initial'=>'jap'],
            ['tipe'=>'SJ-Standar', 'initial'=>'std'],
            ['tipe'=>'Jok Assy', 'initial'=>'ass'],
            ['tipe'=>'Stiker', 'initial'=>'sti'],
            ['tipe'=>'Rol', 'initial'=>'rol'],
            ['tipe'=>'Rotan', 'initial'=>'rot'],
            ['tipe'=>'Tankpad', 'initial'=>'tp'],
            ['tipe'=>'Busa', 'initial'=>'bus'],
            ['tipe'=>'Dll', 'initial'=>'dll'],
        ];
    }

    static function get_tipe_packing() {
        return [
            'bal',
            'colly',
            'dus',
            'rol',
        ];
    }
}
