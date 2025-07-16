<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdatePriceOfBarangs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-price-of-barangs';

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
        // Update the latest price for each barang
        $barangs = \App\Models\Barang::all();
        foreach ($barangs as $barang) {
            $latestPrice = $barang->latestPrice();
            if ($latestPrice) {
                if ($barang->harga_main !== $latestPrice->price) {
                    $harga_total_main = $latestPrice->price * ($barang->jumlah_main / 100);
                    $harga_total_sub = null;
                    if ($barang->jumlah_sub) {
                        $harga_total_sub = $harga_total_main;
                    }
                    try {
                        $barang->update([
                            'harga_main' => $latestPrice->price,
                            'harga_total_main' => $harga_total_main,
                            'harga_total_sub' => $harga_total_sub,
                            'updated_at' => $latestPrice->created_at,
                        ]);
                    } catch (\Throwable $th) {
                        Log::error("Failed to update barang {$barang->id} - {$barang->nama}: $barang->harga_main -> $latestPrice->price - {$th->getMessage()}");
                    }
                }
            }
        }
    }
}
