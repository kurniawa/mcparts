<?php

namespace App\Console\Commands;

use App\Models\Nota;
use Illuminate\Console\Command;

class SeedingTotalPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seeding-total-payment';

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
        // Kemudian update total_payment = harga_total
        Nota::whereNotNull('finished_at')->each(function ($nota) {
            $nota->status_bayar = 'lunas'; // Set status_bayar to 'lunas'
            $nota->total_payment = $nota->harga_total;
            $nota->remaining_payment = 0; // Set remaining_payment to 0 as it is fully paid
            $nota->save();
        });

        Nota::whereNull('finished_at')->each(function ($nota) {
            $nota->status_bayar = 'belum_lunas'; // Set status_bayar to 'belum_lunas'
            $nota->total_payment = 0; // Set total_payment to 0 as it is not fully paid
            $nota->remaining_payment = $nota->harga_total; // Set remaining_payment to harga_total
            $nota->save();
        });
    }
}
