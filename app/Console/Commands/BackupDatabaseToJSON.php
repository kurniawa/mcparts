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
        foreach ($tables as $table) {
            if ($table === 'pelanggans') {
                $data = DB::connection('mysql_old')->table($table)->get()->map(function ($item) {
                    $item->is_reseller = ($item->is_reseller === 'yes') ? 1 : 0;
                    return $item;
                });
            } elseif ($table === 'suppliers') {
                $data = DB::connection('mysql_old')->table($table)->get()->map(function ($item) {
                    $item = json_decode(json_encode($item), true); // Ubah object menjadi array
                    if (isset($item['creator'])) {
                        $item['created_by'] = $item['creator']; // Buat kolom baru
                        unset($item['creator']); // Hapus kolom lama
                    }
                    return $item;
                });
            } elseif ($table === 'pelanggan_kontaks' || $table === 'supplier_kontaks' || $table === 'srjalan_kontaks') {
                $data = DB::connection('mysql_old')->table($table)->get()->map(function ($item) {
                    $item = json_decode(json_encode($item), true); // Ubah object menjadi array
                    if (isset($item['is_aktual'])) {
                        $item['is_actual'] = $item['is_aktual']; // Buat kolom baru
                        unset($item['is_aktual']); // Hapus kolom lama
                        $item['is_actual'] = ($item['is_actual'] === 'yes') ? 1 : 0;
                    }
                    return $item;
                });
            } elseif ($table === 'srjalans') {
                $data = DB::connection('mysql_old')->table($table)->get()->map(function ($item) {
                    $item = json_decode(json_encode($item), true); // Ubah object menjadi array
                    if (isset($item['jml_colly'])) {
                        $item['jumlah_colly'] = $item['jml_colly']; // Buat kolom baru
                        unset($item['jml_colly']); // Hapus kolom lama
                    }
                    if (isset($item['jml_rol'])) {
                        $item['jumlah_rol'] = $item['jml_rol'];
                        unset($item['jml_rol']);
                    }
                    if (isset($item['jml_dus'])) {
                        $item['jumlah_dus'] = $item['jml_dus'];
                        unset($item['jml_dus']);
                    }
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
