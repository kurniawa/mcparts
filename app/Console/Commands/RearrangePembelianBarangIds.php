<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RearrangePembelianBarangIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:rearrange-pembelian-barang-ids';

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
        $pembelian_barangs = \App\Models\PembelianBarang::orderBy('id')->where('id', '>', 350)->get();
        dump($pembelian_barangs);
    }
}
