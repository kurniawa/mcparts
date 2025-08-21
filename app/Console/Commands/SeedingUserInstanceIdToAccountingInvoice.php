<?php

namespace App\Console\Commands;

use App\Models\AccountingInvoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SeedingUserInstanceIdToAccountingInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seeding-user-instance-id-to-accounting-invoice';

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
        // $accounting_invoice = AccountingInvoice::find(13);
        // dump($accounting_invoice->accounting);
        foreach (\App\Models\AccountingInvoice::all() as $accounting_invoice) {
            if ($accounting_invoice->accounting_id != null) {
                $accounting_invoice->user_instance_id = $accounting_invoice->accounting->user_instance_id;
                $accounting_invoice->save();
            }
        }
    }
}
