<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembelianBarang extends Model
{
    use HasFactory;
    protected $guarded =['id'];

    protected $casts = [
        'harga_main' => 'decimal:2',
        'harga_sub'  => 'decimal:2',
        'jumlah_main'  => 'decimal:2',
        'jumlah_sub'  => 'decimal:2',
        'harga_t'  => 'decimal:2',
    ];

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
