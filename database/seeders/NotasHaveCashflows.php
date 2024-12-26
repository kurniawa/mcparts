<?php

namespace Database\Seeders;

use App\Models\Cashflow;
use App\Models\Nota;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class NotasHaveCashflows extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $notas = Nota::all();
        foreach ($notas as $nota) {
            Cashflow::create([
                'nota_id' => $nota->id,
                'user_id' => Auth::user()->id,
                'type' => 'pemasukan',
                'instance_type' => 'Bank',
                'instance_name' => 'BCA',
                'instance_branch' => 'MC',
                'payment_amount' => (string)((int)$nota->harga_total / 100),
            ]);
        }
    }
}
