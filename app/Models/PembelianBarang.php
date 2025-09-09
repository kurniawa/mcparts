<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembelianBarang extends Model
{
    use HasFactory;
    protected $guarded =['id'];

    public function barang() {
        return $this->hasOne(Barang::class, 'id', 'barang_id');
    }
}
