<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipePackingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipe_packings = ['bal', 'colly', 'dus', 'rol'];
        foreach ($tipe_packings as $tipe_packing) {
            DB::table('tipe_packings')->insert([
                'name' => $tipe_packing,
            ]);
        }
    }
}
