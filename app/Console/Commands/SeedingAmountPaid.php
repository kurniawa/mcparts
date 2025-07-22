<?php

namespace App\Console\Commands;

use App\Models\Nota;
use Illuminate\Console\Command;

class SeedingAmountPaid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seeding-amount-paid';

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
        // Kemudian update amount_paid = harga_total
        Nota::whereNotNull('finished_at')->each(function ($nota) {
            $nota->status_bayar = 'lunas'; // Set status_bayar to 'lunas'
            $nota->amount_paid = $nota->harga_total;
            $nota->amount_due = 0; // Set amount_due to 0 as it is fully paid
            $nota->save();
        });

        Nota::whereNull('finished_at')->each(function ($nota) {
            $nota->status_bayar = 'belum_lunas'; // Set status_bayar to 'belum_lunas'
            $nota->amount_paid = 0; // Set amount_paid to 0 as it is not fully paid
            $nota->amount_due = $nota->harga_total; // Set amount_due to harga_total
            $nota->save();
        });
    }
}
