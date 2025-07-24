<?php

namespace App\Console\Commands;

use App\Models\AccountingInvoice;
use Illuminate\Console\Command;

class SeedingAccountingInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seeding-accounting-invoices';

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
        /**
         * Hanya seeding data untuk invoices/notas dengan status_bayar = belum_lunas
         */
        $notas = \App\Models\Nota::where('status_bayar', 'belum_lunas')->get();
        foreach ($notas as $nota) {
            if ($nota->status_bayar === 'belum_lunas' || $nota->status_bayar === 'sebagian') {
                AccountingInvoice::create([
                    'time_key' => $nota->created_at->timestamp,
                    'invoice_id' => $nota->id,
                    'invoice_table' => 'notas',
                    'invoice_number' => $nota->no_nota,
                    'customer_id' => $nota->pelanggan_id,
                    'customer_name' => $nota->pelanggan_nama,
                    'payment_status' => $nota->status_bayar,
                    'amount_due' => $nota->amount_due,
                    'amount_paid' => $nota->amount_paid,
                    'total_amount' => $nota->harga_total,
                ]);
            }
        }
    }
}
