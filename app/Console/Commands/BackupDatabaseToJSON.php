<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BackupDatabaseToJSON extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backup-database-to-json';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup database to JSON file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tables = config('table.list');
        // $concerned_table = ['pelanggans', 'suppliers', 'srjalans'];
        foreach ($tables as $key => $table) {
            if ($table === 'pelanggans') {
                $data = DB::connection('mysql_old')->table($table)->get()->map(function ($item) {
                    $item->is_reseller = ($item->is_reseller === 'yes') ? 1 : 0;
                    return $item;
                });
            } elseif ($table === 'suppliers') {
                $data = DB::connection('mysql_old')->table($table)->get()->map(function ($item) {
                    $item = json_decode(json_encode($item), true); // Ubah object menjadi array
                    if (isset($item['creator'])) {
                        $item['owner_name'] = $item['nama_pemilik']; // Buat kolom baru
                        $item['created_by'] = $item['creator']; // Buat kolom baru
                        $item['updated_by'] = $item['updater']; // Buat kolom baru
                        unset($item['nama_pemilik']); // Hapus kolom lama
                        unset($item['creator']); // Hapus kolom lama
                        unset($item['updater']); // Hapus kolom lama
                    }
                    return $item;
                });
            } elseif ($table === 'alamats') {
                $data = DB::connection('mysql_old')->table($table)->get()->map(function ($item) {
                    $item = json_decode(json_encode($item), true); // Ubah object menjadi array
                    if (isset($item['long'])) {
                        $long = $item['long'];
                        if ($long) {
                            $long = str_replace('"', "'", $long);
                            $item['long'] = $long;
                        }
                    }
                    return $item;
                });
            } elseif ($table === 'pelanggan_kontaks' || $table === 'supplier_kontaks' || $table === 'ekspedisi_kontaks') {
                $data = DB::connection('mysql_old')->table($table)->get()->map(function ($item) {
                    $item = json_decode(json_encode($item), true); // Ubah object menjadi array
                    if (isset($item['is_aktual'])) {
                        $item['is_actual'] = $item['is_aktual']; // Buat kolom baru
                        unset($item['is_aktual']); // Hapus kolom lama
                        $item['is_actual'] = ($item['is_actual'] === 'yes') ? 1 : 0;
                    }
                    return $item;
                });
            } elseif ($table === 'notas') {
                $data = DB::connection('mysql_old')->table($table)->get()->map(function ($item) {
                    $item = json_decode(json_encode($item), true); // Ubah object menjadi array
                    $item['reseller_alamat_id'] = $item['alamat_reseller_id'];
                    $item['reseller_kontak_id'] = $item['kontak_reseller_id'];

                    // Hapus kolom lama
                    unset($item['alamat_reseller_id']);
                    unset($item['kontak_reseller_id']);

                    $cust_long = $item['cust_long'];
                    if ($cust_long) {
                        $cust_long = str_replace('"', "'", $cust_long);
                        $item['cust_long'] = $cust_long;
                    }

                    $cust_kontak = $item['cust_kontak'];
                    if ($cust_kontak) {
                        $cust_kontak = str_replace('"', "'", $cust_kontak);
                        $item['cust_kontak'] = $cust_kontak;
                    }

                    $reseller_long = $item['reseller_long'];
                    if ($reseller_long) {
                        $reseller_long = str_replace('"', "'", $reseller_long);
                        $item['reseller_long'] = $reseller_long;
                    }

                    $reseller_kontak = $item['reseller_kontak'];
                    if ($reseller_kontak) {
                        $reseller_kontak = str_replace('"', "'", $reseller_kontak);
                        $item['reseller_kontak'] = $reseller_kontak;
                    }

                    if ($item['status_bayar'] === 'belum' || $item['status_bayar'] === 'BELUM') {
                        $item['status_bayar'] = 'BELUM-LUNAS';
                    }

                    return $item;
                });
            } elseif ($table === 'srjalans') {
                $data = DB::connection('mysql_old')->table($table)->get()->map(function ($item) {
                    $item = json_decode(json_encode($item), true); // Ubah object menjadi array
                    // Buat kolom baru
                    $item['jumlah_colly'] = $item['jml_colly'];
                    $item['jumlah_rol'] = $item['jml_rol'];
                    $item['jumlah_dus'] = $item['jml_dus'];
                    $item['reseller_alamat_id'] = $item['alamat_reseller_id'];
                    $item['reseller_kontak_id'] = $item['kontak_reseller_id'];
                    $item['ekspedisi_alamat_id'] = $item['alamat_ekspedisi_id'];
                    $item['ekspedisi_kontak_id'] = $item['kontak_ekspedisi_id'];
                    $item['transit_alamat_id'] = $item['alamat_transit_id'];
                    $item['transit_kontak_id'] = $item['kontak_transit_id'];

                    // Hapus kolom lama
                    unset($item['jml_colly']);
                    unset($item['jml_rol']);
                    unset($item['jml_dus']);
                    unset($item['alamat_reseller_id']);
                    unset($item['kontak_reseller_id']);
                    unset($item['alamat_ekspedisi_id']);
                    unset($item['kontak_ekspedisi_id']);
                    unset($item['alamat_transit_id']);
                    unset($item['kontak_transit_id']);

                    $jumlah_packing = $item['jumlah_packing'];
                    if ($jumlah_packing) {
                        $jumlah_packing = str_replace('"', "'", $jumlah_packing);
                        $item['jumlah_packing'] = $jumlah_packing;
                    }

                    $cust_long = $item['cust_long'];
                    if ($cust_long) {
                        $cust_long = str_replace('"', "'", $cust_long);
                        $item['cust_long'] = $cust_long;
                    }

                    $cust_kontak = $item['cust_kontak'];
                    if ($cust_kontak) {
                        $cust_kontak = str_replace('"', "'", $cust_kontak);
                        $item['cust_kontak'] = $cust_kontak;
                    }

                    $reseller_long = $item['reseller_long'];
                    if ($reseller_long) {
                        $reseller_long = str_replace('"', "'", $reseller_long);
                        $item['reseller_long'] = $reseller_long;
                    }

                    $reseller_kontak = $item['reseller_kontak'];
                    if ($reseller_kontak) {
                        $reseller_kontak = str_replace('"', "'", $reseller_kontak);
                        $item['reseller_kontak'] = $reseller_kontak;
                    }

                    $ekspedisi_long = $item['ekspedisi_long'];
                    if ($ekspedisi_long) {
                        $ekspedisi_long = str_replace('"', "'", $ekspedisi_long);
                        $item['ekspedisi_long'] = $ekspedisi_long;
                    }

                    $ekspedisi_kontak = $item['ekspedisi_kontak'];
                    if ($ekspedisi_kontak) {
                        $ekspedisi_kontak = str_replace('"', "'", $ekspedisi_kontak);
                        $item['ekspedisi_kontak'] = $ekspedisi_kontak;
                    }

                    $transit_long = $item['transit_long'];
                    if ($transit_long) {
                        $transit_long = str_replace('"', "'", $transit_long);
                        $item['transit_long'] = $transit_long;
                    }

                    $transit_kontak = $item['transit_kontak'];
                    if ($transit_kontak) {
                        $transit_kontak = str_replace('"', "'", $transit_kontak);
                        $item['transit_kontak'] = $transit_kontak;
                    }

                    return $item;
                });
            } elseif ($table === 'spk_produk_notas') {
                $data = DB::connection('mysql_old')->table($table)->get()->map(function ($item) {
                    $item = json_decode(json_encode($item), true); // Ubah object menjadi array
                    $item['is_price_updated'] = ($item['is_price_updated'] === 'yes') ? 1 : 0;
                    return $item;
                });
            } elseif ($table === 'user_instances') {
                $data = DB::connection('mysql_old')->table($table)->get()->map(function ($item) {
                    $item = json_decode(json_encode($item), true); // Ubah object menjadi array
                    $item['instance_branch'] = $item['branch'];
                    // Hapus kolom lama
                    unset($item['branch']);

                    return $item;
                });
            } elseif ($table === 'accountings') {
                $data = DB::connection('mysql_old')->table($table)->get()->map(function ($item) {
                    $item = json_decode(json_encode($item), true); // Ubah object menjadi array
                    $item['user_instance_type'] = $item['instance_type'];
                    $item['user_instance_name'] = $item['instance_name'];
                    $item['user_instance_branch'] = $item['branch'];
                    // Hapus kolom lama
                    unset($item['instance_type']);
                    unset($item['instance_name']);
                    unset($item['branch']);

                    return $item;
                });
            } else {
                $data = DB::connection('mysql_old')->table($table)->get();
            }

            File::put(storage_path("backup/$table.json"), $data->toJson());
        }

        $this->info('Data has been exported to BackupSeeder.php');
    }
}
