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

    static function label_pelanggans()
    {
        $pelanggan_indirects = Pelanggan::where('reseller_id','!=',null)->get(['id','nama','reseller_id'])->toArray();
        $pelanggan_resellers=array();
        foreach ($pelanggan_indirects as $pelanggan) {
            $reseller = Pelanggan::find($pelanggan['reseller_id'])->toArray();
            $pelanggan_resellers[]=[
                'label'=>"$reseller[nama] - $pelanggan[nama]",
                'value'=>"$reseller[nama] - $pelanggan[nama]",
                'id'=>"$pelanggan[id]",
                'reseller_id'=>"$reseller[id]",
            ];
        }
        $pelanggans=Pelanggan::all(['id','nama as label','nama as value'])->toArray();
        $label_pelanggans = array_merge($pelanggans,$pelanggan_resellers);

        return $label_pelanggans;
    }

}
