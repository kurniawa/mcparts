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
}
