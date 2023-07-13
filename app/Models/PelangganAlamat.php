<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PelangganAlamat extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function alamat()
    {
        return $this->hasOne(Alamat::class, 'id', 'alamat_id');
    }
}
