<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public $timestamps = false;

    static function list_of_kategoris() {
        return [
            [
                'type' => 'UANG MASUK', 'kategori_level_one'=>
                    [
                        ['name'=>'PENJUALAN CASH'],
                        ['name'=>'PENERIMAAN PIUTANG'],
                        ['name'=>'MUTASI DARI KAS KANTOR 1'],
                        ['name'=>'MUTASI DARI KAS KANTOR AKHUN'],
                        ['name'=>'MUTASI DARI KAS BCA MCP'],
                        ['name'=>'MUTASI DARI KAS BCA DMD'],
                        ['name'=>'MUTASI DARI KAS DANAMON MCP'],
                        ['name'=>'MUTASI DARI KAS BRI DMD'],
                        ['name'=>'MUTASI DARI KAS BG'],
                        ['name'=>'BUNGA TABUNGAN'],
                        ['name'=>'TITIPAN TRF'],
                        ['name'=>'PENGGANTI GIRO KOSONG/TOLAKAN KLIRING'],
                        ['name'=>'PENERIMAAN LAIN-LAIN'],
                        ['name'=>'UTANG LAIN-LAIN']
                    ],
            ],
            [
                'type' => 'UANG KELUAR', 'kategori_level_one'=>
                    [
                        ['name'=>'BAYAR HUTANG BAHAN BAKU'],
                        ['name'=>'BELI BAHAN BAKU CASH'],
                        ['name'=>'BAYAR HUTANG BAHAN PENDUKUNG'],
                        ['name'=>'BELI BAHAN PENDUKUNG CASH'],
                        ['name'=>'MUTASI KE KAS KANTOR 1'],
                        ['name'=>'MUTASI KE KAS KANTOR DMD'],
                        ['name'=>'MUTASI KE KAS KANTOR DIAN'],
                        ['name'=>'MUTASI KE KAS KANTOR AKHUN'],
                        ['name'=>'MUTASI KE KAS BCA MCP'],
                        ['name'=>'MUTASI KE KAS BCA DMD'],
                        ['name'=>'MUTASI KE KAS DANAMON MCP'],
                        ['name'=>'MUTASI KE KAS BRI DMD'],
                        ['name'=>'PRIVE'],
                        ['name'=>'CASHBON KARYAWAN'],
                        ['name'=>'REFUND - KELEBIHAN PEMBAYARAN'],
                        ['name'=>'TITIP SIMPAN GAJI KARYAWAN'],
                        ['name'=>'TITIP TRF KELUAR'],
                        ['name'=>'PAJAK BUNGA TABUNGAN'],
                        ['name'=>'BIAYA TENAGA KERJA LANGSUNG', 'kategori_level_two'=>
                            [['name'=>'GAJI DAN UPAH'],
                            ['name'=>'LEMBUR'],
                            ['name'=>'TUNJANGAN KARYAWAN'],
                            ['name'=>'KOMISI PENJUALAN'],]
                        ],
                        ['name'=>'BIAYA BAHAN PENDUKUNG', 'kategori_level_two'=>
                            [['name'=>'PERLENGKAPAN SABLON'],
                            ['name'=>'PERLENGKAPAN JAHIT'],
                            ['name'=>'POLIMAS'],
                            ['name'=>'PACKING'],
                            ['name'=>'JASA BORDIR'],]
                        ],
                        ['name'=>'BIAYA PENGIRIMAN BARANG', 'kategori_level_two'=>
                            [['name'=>'OPERASIONAL PENGIRIMAN'],]
                        ],
                        ['name'=>'BIAYA UTILITAS', 'kategori_level_two'=>
                            [['name'=>'LISTRIK'],
                            ['name'=>'TELEPON'],
                            ['name'=>'INTERNET'],]
                        ],
                        ['name'=>'PAJAK', 'kategori_level_two'=>
                            [['name'=>'PPH PASAL 21'],
                            ['name'=>'PPH PASAL 25 DAN 29'],]
                        ],
                        ['name'=>'BIAYA INVENTARIS (PERALATAN DAN PERLENGKAPAN)', 'kategori_level_two'=>
                            [['name'=>'ATK'],
                            ['name'=>'PERALATAN DAN PERLENGKAPAN PRODUKSI'],
                            ['name'=>'CICILAN MOBIL TRAGA'],]
                        ],
                        ['name'=>'BIAYA MAINTENANCE', 'kategori_level_two'=>
                            [['name'=>'PERAWATAN KENDARAAN'],
                            ['name'=>'PERAWATAN MESIN'],
                            ['name'=>'PERAWATAN INVENTARIS'],]
                        ],
                        ['name'=>'BIAYA LAIN-LAIN', 'kategori_level_two'=>
                            [['name'=>'ADMINISTRASI BANK'],
                            ['name'=>'ENTERTAIN PELANGGAN'],
                            ['name'=>'KUNJUNGAN KE DAERAH'],
                            ['name'=>'LAIN-LAIN'],]
                        ],
                    ],
            ],
        ];
    }
}
// Cangcimen AQUATIC & TERRESTRIAL INDONESIA
// CangciMen AQUATIC & TERRESTRIAL INDONESIA
// CangciMEN AQUATIC & TERRESTRIAL INDONESIA
// CibinongGuy AQUATIC & TERRESTRIAL INDONESIA
