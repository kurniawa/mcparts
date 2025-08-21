<?php

namespace App\Console\Commands;

use App\Models\AccountingInvoice;
use Illuminate\Console\Command;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test';

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
        $accounting_invoice = AccountingInvoice::find(13);
        dump($accounting_invoice->userInstance);
    }
}
