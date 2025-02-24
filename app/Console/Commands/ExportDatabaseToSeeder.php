<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ExportDatabaseToSeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:export-database-to-seeder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export database data to a seeder file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tables = config('table.list');
        foreach ($tables as $table) {
            $data = DB::table($table)->get();
            File::put(storage_path("backup/$table.json"), $data->toJson());
        }

        $this->info('Data has been exported to BackupSeeder.php');
    }
}
