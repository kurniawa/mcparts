<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateStatusBayarPembelian extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pembelian:update-status-bayar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update status_bayar dari format lama ke format baru';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::table('pembelians')
            ->update([
                'status_bayar' => DB::raw("
                    CASE
                        WHEN status_bayar = 'BELUM' THEN 'BELUM_LUNAS'
                        WHEN status_bayar = 'SEBAGIAN' THEN 'SEBAGIAN'
                        WHEN status_bayar = 'LUNAS' THEN 'LUNAS'
                        ELSE status_bayar
                    END
                ")
            ]);

        DB::table('notas')
            ->update([
                'status_bayar' => DB::raw("
                    CASE
                        WHEN status_bayar = 'belum_lunas' THEN 'BELUM_LUNAS'
                        WHEN status_bayar = 'sebagian' THEN 'SEBAGIAN'
                        WHEN status_bayar = 'lunas' THEN 'LUNAS'
                        ELSE status_bayar
                    END
                ")
            ]);

            DB::table('accounting_invoices')
            ->update([
                'payment_status' => DB::raw("
                    CASE
                        WHEN payment_status = 'belum_lunas' THEN 'BELUM_LUNAS'
                        WHEN payment_status = 'sebagian' THEN 'SEBAGIAN'
                        WHEN payment_status = 'lunas' THEN 'LUNAS'
                        ELSE payment_status
                    END
                ")
            ]);

        $this->info('Update status_bayar selesai.');
    }
}
