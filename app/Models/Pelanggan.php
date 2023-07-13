<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    static function alamat_utama($pelanggan_id)
    {
        $pelanggan_alamat_utama = PelangganAlamat::where('pelanggan_id', $pelanggan_id)->where('tipe', 'UTAMA')->first();
        return $pelanggan_alamat_utama;
    }

    static function kontak_aktual($pelanggan_id)
    {
        $pelanggan_kontak_aktual = PelangganKontak::where('pelanggan_id', $pelanggan_id)->where('is_aktual', 'yes')->first();
        return $pelanggan_kontak_aktual;
    }

}
