<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\BilyetGiro;
use Illuminate\Database\Seeder;
// use Illuminate\Support\Facades\DB;

class BilyetGiroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bilyet_giros = [
            [
                'bilyet_number' => 'EE574202',
                'customer_id' => 121,
                'customer_name' => 'DEWI 2F',
                'issuer_name' => 'Dewi Sriwidhayanti',
                'issuer_bank' => 'BCA',
                'issuer_account_number' => '0113216848',
                'beneficiary_name' => "MC Part's CV",
                'beneficiary_bank' => 'BCA',
                'beneficiary_account_number' => '1673008511',
                'amount' => 10500000,
                'received_date' => '2026-08-01',
                'due_date' => '2026-08-08',
                'clearing_date' => '2026-08-11',
                'status' => 'cleared',
            ],
            [
                'bilyet_number' => '000001',
                'customer_id' => null,
                'customer_name' => null,
                'issuer_name' => 'Bambang Widodo',
                'issuer_bank' => 'UOB',
                'issuer_account_number' => '0123456789',
                'beneficiary_name' => 'Budi Agung',
                'beneficiary_bank' => 'XYZ',
                'beneficiary_account_number' => '123456789',
                'amount' => 100000000,
                'received_date' => '2017-12-01',
                'due_date' => '2018-01-31',
                'clearing_date' => '2018-02-15',
                'status' => 'cleared',
            ],
        ];

        foreach ($bilyet_giros as $bilyet_giro) {
            BilyetGiro::create($bilyet_giro);
        }

    }
}
