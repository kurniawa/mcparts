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
                ->orderBy('created_at', 'asc')
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

            
        }
    }
}
