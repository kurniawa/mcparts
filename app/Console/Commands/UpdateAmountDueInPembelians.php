<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateAmountDueInPembelians extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pembelians:update-amount-due';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update amount_due menjadi sama dengan harga_total jika status_bayar BELUM_LUNAS dan amount_due bernilai 0';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai proses update amount_due...');

        // Menggunakan DB query builder untuk efisiensi performa (Mass Update)
        $affectedRows = DB::table('pembelians')
            ->where('status_bayar', 'BELUM_LUNAS')
            ->where(function($query) {
                $query->where('amount_due', 0)
                      ->orWhere('amount_due', 0.0);
            })
            ->update([
                'amount_due' => DB::raw('harga_total'),
                'updated_at' => now() // Opsional: hapus baris ini jika tidak memakai timestamp
            ]);

        $this->info("Proses selesai! Berhasil memperbarui {$affectedRows} data pembelians.");
        
        return Command::SUCCESS;
    }
}
