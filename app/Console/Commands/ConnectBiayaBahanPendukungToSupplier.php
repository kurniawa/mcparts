<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ConnectBiayaBahanPendukungToSupplier extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:connect-biaya-bahan-pendukung-to-supplier';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $transactionNames = \App\Models\TransactionName::where('kategori_level_one', 'BIAYA BAHAN PENDUKUNG')->get();
        foreach ($transactionNames as $transactionName) {
            if (!$transactionName->supplier_id) {
                $supplier = null;
                switch ($transactionName->desc) {
                    case 'BAYAR DJUNAIDI':
                        $supplier = \App\Models\Supplier::where('nama', 'like', '%KO.JUN%')->first();
                        break;
                    case 'BAYAR FAJAR BARU':
                        $supplier = \App\Models\Supplier::where('nama', 'like', '%FAJAR BARU%')->first();
                        break;
                    case 'BAYAR ISMAIL':
                        $supplier = \App\Models\Supplier::where('nama', 'like', '%ISMAIL%')->first();
                        break;
                    case 'BAYAR MAX':
                        $supplier = \App\Models\Supplier::where('nama', 'like', '%MAX%')->first();
                        break;
                    case 'BAYAR MITRA MANDIRI': // ?
                        $supplier = \App\Models\Supplier::where('nama', 'like', '%MITRA%')->first();
                        break;
                    case 'BAYAR ROYAL':
                        $supplier = \App\Models\Supplier::where('nama', 'like', '%ROYAL%')->first();
                        break;
                    case 'BAYAR TOKO BARU':
                        $supplier = \App\Models\Supplier::where('nama', 'like', '%TOKO BARU%')->first();
                        break;
                    case 'BAYAR TOKO KLASIK':
                        $supplier = \App\Models\Supplier::where('nama', 'like', '%KLASIK%')->first();
                        break;
                    case 'BAYAR SUMBER BARU':
                        $supplier = \App\Models\Supplier::where('nama', 'like', '%SUMBER BARU%')->first();
                        break;
                    case 'BAYAR SUMBER METAL':
                        $supplier = \App\Models\Supplier::where('nama', 'like', '%SUMBERMETAL%')->first();
                        break;
                    default:
                        $supplier = null;
                }

                if ($supplier) {
                    $transactionName->supplier_id = $supplier->id;
                    $transactionName->supplier_nama = $supplier->nama;
                    $transactionName->save();
                    $this->info("Updated TransactionName ID {$transactionName->id} with supplier_id {$supplier->id}");
                } else {
                    $this->warn("No matching supplier found for TransactionName ID {$transactionName->id} with desc {$transactionName->desc}");
                }
            }
        }
    }
}
