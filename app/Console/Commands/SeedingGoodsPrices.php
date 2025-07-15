<?php

namespace App\Console\Commands;

use App\Models\GoodsPrice;
use App\Models\PembelianBarang;
use Illuminate\Console\Command;

class SeedingGoodsPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seeding-goods-prices';

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
        $barangs = \App\Models\Barang::all();
        foreach ($barangs as $barang) {
            $pembelian_barangs = PembelianBarang::where('barang_id', $barang->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->unique('harga_main')
                ->values();
            foreach ($pembelian_barangs as $pembelian_barang) {
                GoodsPrice::create([
                    'goods_id' => $barang->id,
                    'goods_slug' => $barang->nama,
                    'supplier_id' => $barang->supplier_id,
                    'supplier_name' => $barang->supplier_nama,
                    'unit_order' => 1,
                    'unit' => $barang->satuan_main,
                    'price' => $pembelian_barang->harga_main, // Default price
                    'created_by' => 'kuruniawa',
                    'created_at' => $pembelian_barang->created_at,
                ]);
            }

            // Update the latest price for each barang
            $latestPrice = $barang->latestPrice();
            if ($latestPrice) {
                if ($barang->harga_main !== $latestPrice->price) {
                    $harga_total_main = $latestPrice->price * $barang->jumlah_main;
                    $harga_total_sub = $harga_total_main;
                    $barang->update([
                        'harga_main' => $latestPrice->price,
                        'harga_total_main' => $harga_total_main,
                        'harga_total_sub' => $harga_total_sub,
                        'updated_at' => $latestPrice->created_at,
                    ]);
                }
            }
        }
    }
}
