<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdatePembelianPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pembelians:update-payment';

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
        $this->info('Memulai update data pembelian...');

        // 1. Update untuk status 'LUNAS'
        // amount_due = 0, amount_paid = harga_total
        DB::table('pembelians')
            ->where('status_bayar', 'LUNAS')
            ->update([
                'amount_due' => 0.00,
                'amount_paid' => DB::raw('harga_total'),
                'balance_used' => DB::raw('harga_total')
            ]);

        // 2. Update untuk status 'BELUM_LUNAS'
        // amount_due = harga_total, amount_paid = 0
        DB::table('pembelians')
            ->where('status_bayar', 'BELUM_LUNAS')
            ->update([
                'amount_due' => DB::raw('harga_total'),
                'amount_paid' => 0.00
            ]);

        $this->info('Update selesai!');
    }
}
