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
}
