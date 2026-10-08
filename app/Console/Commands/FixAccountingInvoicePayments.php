<?php

namespace App\Console\Commands;

use App\Models\AccountingInvoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class FixAccountingInvoicePayments extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'accounting:fix-invoice-payments';

    /**
     * The console command description.
     */
    protected $description = 'Fix amount_paid_total and amount_paid in accounting_invoices';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting accounting invoice payment correction...');
        $this->newLine();

        try {
            DB::transaction(function () {

                /*
                 * Ambil semua accounting_invoices.
                 *
                 * Kita membutuhkan:
                 * - id
                 * - invoice_id
                 * - invoice_table
                 * - time_key
                 * - amount_paid
                 *
                 * amount_paid saat ini masih merupakan nilai akumulasi
                 * pembayaran dari data lama.
                 */
                $invoices = AccountingInvoice::query()
                    ->select([
                        'id',
                        'invoice_id',
                        'invoice_table',
                        'time_key',
                        'amount_paid',
                    ])
                    ->orderBy('invoice_id')
                    ->orderBy('invoice_table')
                    ->orderBy('time_key')
                    ->get();

                $total = $invoices->count();

                if ($total === 0) {
                    $this->info('Tidak ada data accounting_invoices yang perlu diproses.');

                    return;
                }

                $this->info("Total data yang akan diproses: {$total}");
                $this->newLine();

                /*
                 * Menyimpan amount_paid_total sebelumnya
                 * untuk setiap kombinasi invoice_id + invoice_table.
                 *
                 * Contoh:
                 *
                 * invoice_id = 10
                 * invoice_table      = invoices
                 *
                 * previous = nilai amount_paid_total
                 * dari history sebelumnya.
                 */
                $previousAmounts = [];

                $bar = $this->output->createProgressBar($total);
                $bar->start();

                foreach ($invoices as $invoice) {

                    /*
                     * Buat key berdasarkan invoice_id + invoice_table.
                     *
                     * Contoh:
                     * 10|invoices
                     */
                    $groupKey = $invoice->invoice_id . '|' . $invoice->invoice_table;

                    /*
                     * amount_paid lama adalah nilai akumulasi.
                     *
                     * Simpan terlebih dahulu ke amount_paid_total.
                     */
                    $amountPaidTotal = $invoice->amount_paid;

                    /*
                     * Jika ini adalah history pertama
                     * untuk invoice_id + invoice_table tersebut,
                     * maka amount_paid tetap sama.
                     *
                     * Jika bukan history pertama:
                     *
                     * amount_paid baru =
                     * amount_paid_total sekarang
                     * - amount_paid_total sebelumnya
                     */
                    if (!array_key_exists($groupKey, $previousAmounts)) {

                        $amountPaid = $amountPaidTotal;

                    } else {

                        $amountPaid = $amountPaidTotal - $previousAmounts[$groupKey];
                    }

                    /*
                     * Update database.
                     */
                    AccountingInvoice::where('id', $invoice->id)
                        ->update([
                            'amount_paid_total' => $amountPaidTotal,
                            'amount_paid'       => $amountPaid,
                        ]);

                    /*
                     * Simpan nilai akumulasi saat ini
                     * sebagai nilai sebelumnya untuk history berikutnya.
                     */
                    $previousAmounts[$groupKey] = $amountPaidTotal;

                    $bar->advance();
                }

                $bar->finish();

                $this->newLine(2);
            });

            $this->info('Accounting invoice payment correction completed successfully.');

            return self::SUCCESS;

        } catch (Throwable $e) {

            $this->newLine(2);

            $this->error('Accounting invoice payment correction FAILED.');

            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}