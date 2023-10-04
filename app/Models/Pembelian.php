<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    use HasFactory;
    protected $guarded=['id'];

    static function lengkapi_data_pembelian($pembelian, $pembelian_temp) {
        $isi = array();
        $harga_total = 0;
        $status_bayar = 'BELUM';
        $jumlah_lunas = 0;
        $keterangan_bayar = '';
        $tanggal_lunas = null;

        $pembelian_barangs_before = PembelianBarang::where('pembelian_id', $pembelian->id)->orderBy('created_at')->get();

        foreach ($pembelian_barangs_before as $pembelian_barang_before) {
            $harga_total += (int)$pembelian_barang_before->harga_t;
            $exist_satuan_main = false;
            $exist_satuan_sub = false;
            if (count($isi) !== 0) {
                for ($i=0; $i < count($isi); $i++) {
                    if ($isi[$i]['satuan'] === $pembelian_barang_before->satuan_main) {
                        $isi[$i]['jumlah'] += (int)($pembelian_barang_before->jumlah_main);
                        $exist_satuan_main = true;
                    }
                    if ($isi[$i]['satuan'] === $pembelian_barang_before->satuan_sub) {
                        $isi[$i]['jumlah'] += (int)($pembelian_barang_before->jumlah_sub);
                        $exist_satuan_sub = true;
                    }
                }
            }
            if (!$exist_satuan_main) {
                $isi[] = [
                    'satuan' => $pembelian_barang_before->satuan_main,
                    'jumlah' => (int)($pembelian_barang_before->jumlah_main),
                ];
            }
            if (!$exist_satuan_sub) {
                if ($pembelian_barang_before->satuan_sub !== null) {
                    $isi[] = [
                        'satuan' => $pembelian_barang_before->satuan_sub,
                        'jumlah' => (int)($pembelian_barang_before->jumlah_sub),
                    ];
                }
            }
            if ($pembelian_barang_before->status_bayar === 'LUNAS') {
                $jumlah_lunas++;
            }
            $keterangan_bayar = $pembelian_barang_before->keterangan_bayar;
            if ($tanggal_lunas === null) {
                $tanggal_lunas = $pembelian_temp->tanggal_lunas;
            } else {
                if ($pembelian_temp->tanggal_lunas !== null) {
                    if (date('Y-m-d H:i:s', strtotime($tanggal_lunas)) < date('Y-m-d H:i:s', strtotime($pembelian_temp->tanggal_lunas))) {
                        $tanggal_lunas = $pembelian_temp->tanggal_lunas;
                    }
                }
            }
        }

        if ($jumlah_lunas === count($pembelian_barangs_before)) {
            $status_bayar = 'LUNAS';
        } elseif ($jumlah_lunas < count($pembelian_barangs_before) && $jumlah_lunas > 0) {
            $status_bayar = 'SEBAGIAN';
        }

        $nomor_nota = null;
        if ($pembelian->supplier_nama !== $pembelian_temp->supplier) {
            if (str_contains($pembelian_temp->supplier, 'MAX')) {
                $nomor_nota = explode(' ', trim($pembelian_temp->supplier))[1];
            } elseif (str_contains($pembelian_temp->supplier, 'ROYAL')) {
                $nomor_nota = explode(' ', trim($pembelian_temp->supplier))[1];
            } elseif (str_contains($pembelian_temp->supplier, 'TOKO BARU')) {
                $nomor_nota = explode(' ', trim($pembelian_temp->supplier))[2];
            } elseif (str_contains($pembelian_temp->supplier, 'ISMAIL')) {
                $nomor_nota = "N-$pembelian->id";
            }
        } else {
            $nomor_nota = "N-$pembelian->id";
        }

        if (!$nomor_nota) {
            dump("nomor nota? pembelian->supplier_nama: $pembelian->supplier_nama -- pembelian_temp->supplier: $pembelian_temp->supplier");
        }

        return array($isi, $harga_total, $status_bayar, $keterangan_bayar, $tanggal_lunas, $nomor_nota);

    }
}
