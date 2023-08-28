<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionName extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public $timestamps = false;

    static function list_of_transaction_names() {
        $list_of_transaction_names_albert = [
            [
                'user_id'=>7,
                'username'=>'Albert21',
                'desc'=>'CASHBON MINGGUAN',
                'kategori_type'=>'UANG KELUAR',
                'kategori_level_one'=>'BIAYA TENAGA KERJA LANGSUNG',
                'kategori_level_two'=>'GAJI DAN UPAH',
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>null,
                'pelanggan_nama'=>null,
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],
            [
                'user_id'=>7,
                'username'=>'Albert21',
                'desc'=>'UPAH MINGGUAN',
                'kategori_type'=>'UANG KELUAR',
                'kategori_level_one'=>'BIAYA TENAGA KERJA LANGSUNG',
                'kategori_level_two'=>'GAJI DAN UPAH',
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>null,
                'pelanggan_nama'=>null,
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],
            [
                'user_id'=>7,
                'username'=>'Albert21',
                'desc'=>'DLL',
                'kategori_type'=>'UANG KELUAR',
                'kategori_level_one'=>'BIAYA LAIN-LAIN',
                'kategori_level_two'=>'LAIN-LAIN',
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>null,
                'pelanggan_nama'=>null,
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],
            [
                'user_id'=>7,
                'username'=>'Albert21',
                'desc'=>'MUTASI DARI KAS KANTOR AK',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'BIAYA LAIN-LAIN',
                'kategori_level_two'=>'LAIN-LAIN',
                'related_user_id'=>2,
                'related_username'=>'kuruniawa',
                'related_desc'=>'MUTASI KE KAS KANTOR ALBERT',
                'pelanggan_id'=>null,
                'pelanggan_nama'=>null,
                'related_user_instance_id'=>7,
                'related_user_instance_type'=>'safe',
                'related_user_instance_name'=>'storage',
                'related_user_instance_branch'=>'Akhun',
            ],
            [
                'user_id'=>7,
                'username'=>'Albert21',
                'desc'=>'PBK TLP',
                'kategori_type'=>'UANG KELUAR',
                'kategori_level_one'=>'BIAYA UTILITAS',
                'kategori_level_two'=>'TELEPON',
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>null,
                'pelanggan_nama'=>null,
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],
            [
                'user_id'=>7,
                'username'=>'Albert21',
                'desc'=>'PRIVE DMD',
                'kategori_type'=>'UANG KELUAR',
                'kategori_level_one'=>'PRIVE',
                'kategori_level_two'=>null,
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>null,
                'pelanggan_nama'=>null,
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],
            [
                'user_id'=>7,
                'username'=>'Albert21',
                'desc'=>'REIMBURSE U.MKN',
                'kategori_type'=>'UANG KELUAR',
                'kategori_level_one'=>'BIAYA TENAGA KERJA LANGSUNG',
                'kategori_level_two'=>'TUNJANGAN KARYAWAN',
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>null,
                'pelanggan_nama'=>null,
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],
            [
                'user_id'=>7,
                'username'=>'Albert21',
                'desc'=>'REIMBURSE U.BEROBAT',
                'kategori_type'=>'UANG KELUAR',
                'kategori_level_one'=>'BIAYA TENAGA KERJA LANGSUNG',
                'kategori_level_two'=>'TUNJANGAN KARYAWAN',
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>null,
                'pelanggan_nama'=>null,
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],
            [
                'user_id'=>7,
                'username'=>'Albert21',
                'desc'=>'REIMBURSE U.KESEHATAN',
                'kategori_type'=>'UANG KELUAR',
                'kategori_level_one'=>'BIAYA TENAGA KERJA LANGSUNG',
                'kategori_level_two'=>'TUNJANGAN KARYAWAN',
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>null,
                'pelanggan_nama'=>null,
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],
            [
                'user_id'=>7,
                'username'=>'Albert21',
                'desc'=>'REIMBURSE U.TRANSPORT',
                'kategori_type'=>'UANG KELUAR',
                'kategori_level_one'=>'BIAYA TENAGA KERJA LANGSUNG',
                'kategori_level_two'=>'TUNJANGAN KARYAWAN',
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>null,
                'pelanggan_nama'=>null,
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],
            [
                'user_id'=>7,
                'username'=>'Albert21',
                'desc'=>'SISA GAJI',
                'kategori_type'=>'UANG KELUAR',
                'kategori_level_one'=>'BIAYA TENAGA KERJA LANGSUNG',
                'kategori_level_two'=>'GAJI DAN UPAH',
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>null,
                'pelanggan_nama'=>null,
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],
            [
                'user_id'=>7,
                'username'=>'Albert21',
                'desc'=>'UPAH MINGGUAN AKHIR',
                'kategori_type'=>'UANG KELUAR',
                'kategori_level_one'=>'BIAYA TENAGA KERJA LANGSUNG',
                'kategori_level_two'=>'GAJI DAN UPAH',
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>null,
                'pelanggan_nama'=>null,
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],
            [
                'user_id'=>7,
                'username'=>'Albert21',
                'desc'=>'UPAH BULANAN',
                'kategori_type'=>'UANG KELUAR',
                'kategori_level_one'=>'BIAYA TENAGA KERJA LANGSUNG',
                'kategori_level_two'=>'GAJI DAN UPAH',
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>null,
                'pelanggan_nama'=>null,
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],
            [
                'user_id'=>7,
                'username'=>'Albert21',
                'desc'=>'PPH PS 21',
                'kategori_type'=>'UANG KELUAR',
                'kategori_level_one'=>'PAJAK',
                'kategori_level_two'=>'PPH PASAL 21',
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>null,
                'pelanggan_nama'=>null,
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],
            [
                'user_id'=>7,
                'username'=>'Albert21',
                'desc'=>'PPH PS 25',
                'kategori_type'=>'UANG KELUAR',
                'kategori_level_one'=>'PAJAK',
                'kategori_level_two'=>'PPH PASAL 25 DAN 29',
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>null,
                'pelanggan_nama'=>null,
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],
            [
                'user_id'=>7,
                'username'=>'Albert21',
                'desc'=>'MAKAN SIANG',
                'kategori_type'=>'UANG KELUAR',
                'kategori_level_one'=>'BIAYA TENAGA KERJA LANGSUNG',
                'kategori_level_two'=>'GAJI DAN UPAH',
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>null,
                'pelanggan_nama'=>null,
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],
            [
                'user_id'=>7,
                'username'=>'Albert21',
                'desc'=>'INDIHOME MCP',
                'kategori_type'=>'UANG KELUAR',
                'kategori_level_one'=>'BIAYA UTILITAS',
                'kategori_level_two'=>'INTERNET',
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>null,
                'pelanggan_nama'=>null,
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],
        ];

        $list_of_transaction_names_bca_dmd = [
            // PIUTANG
            [
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'PIUTANG 3M - SURABAYA',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'kategori_level_two'=>null,
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>2,
                'pelanggan_nama'=>'3M',
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],
            [
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'BERJAYA MOTOR',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'kategori_level_two'=>null,
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>17,
                'pelanggan_nama'=>'Berjaya Motor',
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],
            [
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'PIUTANG BUDI STIKER - MEDAN',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'kategori_level_two'=>null,
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>20,
                'pelanggan_nama'=>'Budi Stiker',
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],
            [
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'PIUTANG DARWIS MOTOR',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'kategori_level_two'=>null,
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>26,
                'pelanggan_nama'=>'Darwis Motor',
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],
            [
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'PIUTANG GLOBAL STIKER',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'kategori_level_two'=>null,
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>33,
                'pelanggan_nama'=>'Global Stiker',
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],
            [
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'PIUTANG HOKKY MOTOR - MANADO',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'kategori_level_two'=>null,
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>37,
                'pelanggan_nama'=>'Hokky Motor',
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],
            [
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'PIUTANG IPM - MAKASSAR',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'kategori_level_two'=>null,
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>42,
                'pelanggan_nama'=>'Indo Putra Mandiri',
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],
            [
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'PIUTANG JAYA MOTOR - MANADO',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'kategori_level_two'=>null,
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>45,
                'pelanggan_nama'=>'Jaya Motor',
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],
            [
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'PIUTANG KMS Motor',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'kategori_level_two'=>null,
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>55,
                'pelanggan_nama'=>'KMS Motor',
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],
            [
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'PIUTANG MILLENIUM MOTOR - PALEMBANG',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'kategori_level_two'=>null,
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>58,
                'pelanggan_nama'=>'Millenium Motor',
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],
            [
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'PIUTANG NAGATA - SINTANG',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'kategori_level_two'=>null,
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>64,
                'pelanggan_nama'=>'Nagata Motor',
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],
            [
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'PIUTANG NGK MOTOR - JAMBI',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'kategori_level_two'=>null,
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>65,
                'pelanggan_nama'=>'NGK Motor',
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],
            [
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'PIUTANG WA - MAKASSAR',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'kategori_level_two'=>null,
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>100,
                'pelanggan_nama'=>'WA',
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],
            [
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'PIUTANG WK MOTOR - SEI PINYUH',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'kategori_level_two'=>null,
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>101,
                'pelanggan_nama'=>'WK Motor',
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],
            // END - PIUTANG
            [
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'MUTASI KE KAS KANTOR AKHUN',
                'kategori_type'=>'UANG KELUAR',
                'kategori_level_one'=>'MUTASI KE KAS KANTOR AKHUN',
                'kategori_level_two'=>null,
                'related_user_id'=>2,
                'related_username'=>'kuruniawa',
                'related_desc'=>'MUTASI DARI KAS BCA DMD',
                'pelanggan_id'=>null,
                'pelanggan_nama'=>null,
                'related_user_instance_id'=>7,
                'related_user_instance_type'=>'safe',
                'related_user_instance_name'=>'storage',
                'related_user_instance_branch'=>'Akhun',
            ],
            [
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'BIAYA ADMIN',
                'kategori_type'=>'UANG KELUAR',
                'kategori_level_one'=>'MUTASI KE KAS KANTOR AKHUN',
                'kategori_level_two'=>null,
                'related_user_id'=>2,
                'related_username'=>'kuruniawa',
                'related_desc'=>'MUTASI DARI KAS BCA DMD',
                'pelanggan_id'=>null,
                'pelanggan_nama'=>null,
                'related_user_instance_id'=>7,
                'related_user_instance_type'=>'safe',
                'related_user_instance_name'=>'storage',
                'related_user_instance_branch'=>'Akhun',
            ],
        ];

        $list_of_transaction_names_bri_dmd = [
            // PIUTANG
            [
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'PIUTANG AGUS - CITEUREUP',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'kategori_level_two'=>null,
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>105,
                'pelanggan_nama'=>'Bpk.AGUS',
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],[
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'PIUTANG ANDI MC',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'kategori_level_two'=>null,
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>9,
                'pelanggan_nama'=>'Andi MC',
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],[
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'PIUTANG ALINDO',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'kategori_level_two'=>null,
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>8,
                'pelanggan_nama'=>'Alindo SM',
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],[
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'PIUTANG FORTUNER MOTOR - LUWUK',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'kategori_level_two'=>null,
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>30,
                'pelanggan_nama'=>'Fortuner Motor (Luwuk)',
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],[
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'PIUTANG JHON MOTOR - MAKASSAR',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'kategori_level_two'=>null,
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>48,
                'pelanggan_nama'=>'Jhon Motor',
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],[
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'PIUTANG MITRA JAYA MOTOR',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'kategori_level_two'=>null,
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>59,
                'pelanggan_nama'=>'Mitra Jaya Motor',
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],[
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'PIUTANG PANIKI MOTOR - MANADO',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'kategori_level_two'=>null,
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>107,
                'pelanggan_nama'=>'PANIKI MOTOR',
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],[
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'PIUTANG SETIA BUDI MOTOR - BANJARMASIN',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'kategori_level_two'=>null,
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>81,
                'pelanggan_nama'=>'Setia Budi',
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],[
                'user_id'=>2,
                'username'=>'kuruniawa',
                'desc'=>'PIUTANG ZONA MOTOR - KETAPANG',
                'kategori_type'=>'UANG MASUK',
                'kategori_level_one'=>'PENERIMAAN PIUTANG',
                'kategori_level_two'=>null,
                'related_user_id'=>null,
                'related_username'=>null,
                'related_desc'=>null,
                'pelanggan_id'=>104,
                'pelanggan_nama'=>'Zona Motor',
                'related_user_instance_id'=>null,
                'related_user_instance_type'=>null,
                'related_user_instance_name'=>null,
                'related_user_instance_branch'=>null,
            ],
            // END - PIUTANG
        ];

        return array($list_of_transaction_names_albert, $list_of_transaction_names_bca_dmd, $list_of_transaction_names_bri_dmd);
    }
}
