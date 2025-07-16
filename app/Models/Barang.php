<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function goodsPrices()
    {
        return $this->hasMany(GoodsPrice::class, 'goods_id');
    }

    public function latestPrice()
    {
        return $this->goodsPrices()->latest('created_at')->first();
    }

    public function pembelians()
    {
        $pembelian_barangs = PembelianBarang::where('barang_id', $this->id)->orderByDesc('created_at')->limit(100)->get();
        // dd($pembelian_barangs);
        $pembelians = [];
        $pembelians_barangs = [];
        foreach ($pembelian_barangs as $pembelian_barang) {
            $pembelian = Pembelian::find($pembelian_barang->pembelian_id);
            if ($pembelian) {
                $pembelians[] = $pembelian;
                $pembelians_barangs[] = $pembelian_barang;
            }
        }
        return array($pembelians, $pembelian_barangs);
    }
}
