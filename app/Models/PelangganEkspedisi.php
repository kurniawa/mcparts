<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PelangganEkspedisi extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    static function data($pelanggan_id, $is_transit) {
        $pelanggan_ekspedisi_utama = null;
        if ($is_transit) {
            $pelanggan_ekspedisi_utama=PelangganEkspedisi::where('pelanggan_id',$pelanggan_id)->where('is_transit','yes')->where('tipe','UTAMA')->first();
        } else {
            $pelanggan_ekspedisi_utama=PelangganEkspedisi::where('pelanggan_id',$pelanggan_id)->where('tipe','UTAMA')->first();
        }
        $ekspedisi_id = $ekspedisi_kontak_id=$ekspedisi_alamat_id=$ekspedisi_nama=$ekspedisi_long=$ekspedisi_short=$ekspedisi_kontak=null;
        if ($pelanggan_ekspedisi_utama!==null) {
            $ekspedisi_id=$pelanggan_ekspedisi_utama['ekspedisi_id'];
            $success_logs[]="Ditemukan ekspedisi utama ID:$ekspedisi_id";
            $ekspedisi=Ekspedisi::find($ekspedisi_id);
            $ekspedisi_nama=$ekspedisi['nama'];
            $ekspedisi_alamat=EkspedisiAlamat::where('ekspedisi_id',$ekspedisi_id)->where('tipe','UTAMA')->first();
            if ($ekspedisi_alamat!==null) {
                $alamat_ekspedisi=Alamat::find($ekspedisi_alamat['alamat_id']);
                $ekspedisi_alamat_id=$alamat_ekspedisi['id'];
                $ekspedisi_long=$alamat_ekspedisi['long'];
                $ekspedisi_short=$alamat_ekspedisi['short'];
            }
            $ekspedisi_kontak=EkspedisiKontak::where('ekspedisi_id',$ekspedisi_id)->where('is_aktual','yes')->first();
            if ($ekspedisi_kontak!==null) {
                $ekspedisi_kontak_id=$ekspedisi_kontak['id'];
                $ekspedisi_kontak=json_encode($ekspedisi_kontak->toArray());
            }
        }

        if ($is_transit) {
            return [
                'transit_id' => $ekspedisi_id,
                'transit_nama' => $ekspedisi_nama,
                'transit_alamat_id' => $ekspedisi_alamat_id,
                'transit_kontak_id' => $ekspedisi_kontak_id,
                'transit_long' => $ekspedisi_long,
                'transit_short' => $ekspedisi_short,
                'transit_kontak' => $ekspedisi_kontak,
            ];
        } else {
            return [
                'ekspedisi_id' => $ekspedisi_id,
                'ekspedisi_nama' => $ekspedisi_nama,
                'ekspedisi_alamat_id' => $ekspedisi_alamat_id,
                'ekspedisi_kontak_id' => $ekspedisi_kontak_id,
                'ekspedisi_long' => $ekspedisi_long,
                'ekspedisi_short' => $ekspedisi_short,
                'ekspedisi_kontak' => $ekspedisi_kontak,
            ];
        }
    }
}
