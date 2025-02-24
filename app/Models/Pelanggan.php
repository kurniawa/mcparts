<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        $pelanggan_kontak_aktual = PelangganKontak::where('pelanggan_id', $pelanggan_id)->where('is_actual', true)->first();
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

    static function data($pelanggan_id) {
        // Data Pelanggan
        $pelanggan=Pelanggan::find($pelanggan_id);
        $nama = $pelanggan->nama;

        // Data Pelanggan - Alamat
        $alamat_id=$long=$short=null;
        $pelanggan_alamat=PelangganAlamat::where('pelanggan_id',$pelanggan['id'])->where('tipe','UTAMA')->first();
        if ($pelanggan_alamat!==null) {
            $alamat=Alamat::find($pelanggan_alamat['alamat_id']);
            $long=$alamat['long'];
            $short=$alamat['short'];
        }
        // Data Pelanggan - Kontak
        $kontak_id = $kontak = null;
        $kontak=PelangganKontak::where('pelanggan_id',$pelanggan['id'])->where('is_actual','yes')->first();
        if ($kontak !== null) {
            $kontak_id = $kontak->id;
        }

        return [
            "nama" => $nama,
            "alamat_id" => $alamat_id,
            "long" => $long,
            "short" => $short,
            "kontak" => $kontak,
            "kontak_id" => $kontak_id,
        ];
    }

    static function data_pelanggan_reseller_ekspedisi_transit($nota) {
        // PELANGGAN
        $alamat_id = null;
        $kontak_id = null;
        $cust_long = null;
        $cust_short = null;
        $cust_kontak = null;
        // RESELLER
        $reseller_alamat_id = null;
        $reseller_kontak_id = null;
        $reseller_long = null;
        $reseller_short = null;
        $reseller_kontak = null;
        // EKSPEDISI
        $ekspedisi_id = null;
        $ekspedisi_nama = null;
        $ekspedisi_alamat_id = null;
        $ekspedisi_kontak_id = null;
        $ekspedisi_long = null;
        $ekspedisi_short = null;
        $ekspedisi_kontak = null;
        // TRANSIT
        $transit_id = null;
        $transit_nama = null;
        $transit_alamat_id = null;
        $transit_kontak_id = null;
        $transit_long = null;
        $transit_short = null;
        $transit_kontak = null;
        $nota_srjalans = NotaSrjalan::where('nota_id',$nota->id)->get();
        $srjalan_acuan = null;
        if (count($nota_srjalans) !== 0) {
            $srjalan_acuan = Srjalan::find($nota_srjalans[0]->srjalan_id);
            // PELANGGAN
            $alamat_id = $srjalan_acuan->alamat_id;
            $kontak_id = $srjalan_acuan->kontak_id;
            $cust_long = $srjalan_acuan->cust_long;
            $cust_short = $srjalan_acuan->cust_short;
            $cust_kontak = $srjalan_acuan->cust_kontak;
            // RESELLER
            $reseller_alamat_id = $srjalan_acuan->reseller_alamat_id;
            $reseller_kontak_id = $srjalan_acuan->reseller_kontak_id;
            $reseller_long = $srjalan_acuan->reseller_long;
            $reseller_short = $srjalan_acuan->reseller_short;
            $reseller_kontak = $srjalan_acuan->reseller_kontak;
            // EKSPEDISI
            $ekspedisi_id = $srjalan_acuan->ekspedisi_id;
            $ekspedisi_nama = $srjalan_acuan->ekspedisi_nama;
            $ekspedisi_alamat_id = $srjalan_acuan->ekspedisi_alamat_id;
            $ekspedisi_kontak_id = $srjalan_acuan->ekspedisi_kontak_id;
            $ekspedisi_long = $srjalan_acuan->ekspedisi_long;
            $ekspedisi_short = $srjalan_acuan->ekspedisi_short;
            $ekspedisi_kontak = $srjalan_acuan->ekspedisi_kontak;
            // TRANSIT
            $transit_id = $srjalan_acuan->transit_id;
            $transit_nama = $srjalan_acuan->transit_nama;
            $transit_alamat_id = $srjalan_acuan->transit_alamat_id;
            $transit_kontak_id = $srjalan_acuan->transit_kontak_id;
            $transit_long = $srjalan_acuan->transit_long;
            $transit_short = $srjalan_acuan->transit_short;
            $transit_kontak = $srjalan_acuan->transit_kontak;
        } else {
            $pelanggan_data = Pelanggan::data($nota->pelanggan_id);
            $alamat_id = $pelanggan_data['alamat_id'];
            $kontak_id = $pelanggan_data['kontak_id'];
            $cust_long = $pelanggan_data['long'];
            $cust_short = $pelanggan_data['short'];
            $cust_kontak = $pelanggan_data['kontak'];
            if ($nota->reseller_id !== null) {
                $reseller_data = Pelanggan::data($nota->reseller_id);
                $reseller_alamat_id = $reseller_data['alamat_id'];
                $reseller_kontak_id = $reseller_data['kontak_id'];
                $reseller_long = $reseller_data['long'];
                $reseller_short = $reseller_data['short'];
                $reseller_kontak = $reseller_data['kontak'];
            }
            $ekspedisi_data = PelangganEkspedisi::data($nota->pelanggan_id, false);
            // EKSPEDISI
            $ekspedisi_id = $ekspedisi_data['ekspedisi_id'];
            $ekspedisi_nama = $ekspedisi_data['ekspedisi_nama'];
            $ekspedisi_alamat_id = $ekspedisi_data['ekspedisi_alamat_id'];
            $ekspedisi_kontak_id = $ekspedisi_data['ekspedisi_kontak_id'];
            $ekspedisi_long = $ekspedisi_data['ekspedisi_long'];
            $ekspedisi_short = $ekspedisi_data['ekspedisi_short'];
            $ekspedisi_kontak = $ekspedisi_data['ekspedisi_kontak'];
            // TRANSIT
            $transit_data = PelangganEkspedisi::data($nota->pelanggan_id, true);
            $transit_id = $transit_data['transit_id'];
            $transit_nama = $transit_data['transit_nama'];
            $transit_alamat_id = $transit_data['transit_alamat_id'];
            $transit_kontak_id = $transit_data['transit_kontak_id'];
            $transit_long = $transit_data['transit_long'];
            $transit_short = $transit_data['transit_short'];
            $transit_kontak = $transit_data['transit_kontak'];
        }

        return [
            // PELANGGAN
            'alamat_id' => $alamat_id,
            'kontak_id' => $kontak_id,
            'cust_long' => $cust_long,
            'cust_short' => $cust_short,
            'cust_kontak' => $cust_kontak,
            // RESELLER
            'reseller_alamat_id' => $reseller_alamat_id,
            'reseller_kontak_id' => $reseller_kontak_id,
            'reseller_long' => $reseller_long,
            'reseller_short' => $reseller_short,
            'reseller_kontak' => $reseller_kontak,
            // EKSPEDISI
            'ekspedisi_id' => $ekspedisi_id,
            'ekspedisi_nama' => $ekspedisi_nama,
            'ekspedisi_alamat_id' => $ekspedisi_alamat_id,
            'ekspedisi_kontak_id' => $ekspedisi_kontak_id,
            'ekspedisi_long' => $ekspedisi_long,
            'ekspedisi_short' => $ekspedisi_short,
            'ekspedisi_kontak' => $ekspedisi_kontak,
            // TRANSIT
            'transit_id' => $transit_id,
            'transit_nama' => $transit_nama,
            'transit_alamat_id' => $transit_alamat_id,
            'transit_kontak_id' => $transit_kontak_id,
            'transit_long' => $transit_long,
            'transit_short' => $transit_short,
            'transit_kontak' => $transit_kontak,
        ];
    }

    static function bentuks() {
        return ['individu', 'PT', 'CV', 'Toko', 'Gudang'];
    }

    function reseller() : HasOne {
        return $this->hasOne(Pelanggan::class, 'id', 'reseller_id');
    }
}
