<?php

namespace App\Console\Commands;

use App\Models\AccountingInvoice;
use Illuminate\Console\Command;

class DeleteUnusedAccountingInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accounting:delete-unused-invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menghapus accounting invoices yang amount_paid dan balance_used bernilai 0';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = AccountingInvoice::query()
            ->where('amount_paid', 0)
            ->where('balance_used', 0)
            ->delete();

        $this->info("Berhasil menghapus {$count} accounting invoice.");

        return Command::SUCCESS;
    }
}
