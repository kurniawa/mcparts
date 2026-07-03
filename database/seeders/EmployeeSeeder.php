<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = [
            ['user_id' => null, 'full_name' => 'Demardi', 'given_name' => 'Demardi', 'family_name' => null, 'preferred_name' => 'Demardi', 'birthday' => '1990-01-01', 'gender' => 'male', 'origin' => 'Jakarta', 'domicile' => 'Karanggan', 'email' => null, 'phone' => '+628129335218', 'start_date' => '2023-01-01', 'status' => 'active', 'created_by' => 2],
            ['user_id' => null, 'full_name' => 'Albert', 'given_name' => 'Albert', 'family_name' => null, 'preferred_name' => 'Albert', 'birthday' => '1990-01-01', 'gender' => 'male', 'origin' => 'Cibinong', 'domicile' => 'Cibinong', 'email' => null, 'phone' => '+6281286556500', 'start_date' => '2023-01-01', 'status' => 'active', 'created_by' => 2],
            ['user_id' => null, 'full_name' => 'Lie Dian Putra', 'given_name' => 'Dian', 'family_name' => 'Lie', 'preferred_name' => 'Dian', 'birthday' => '1990-01-01', 'gender' => 'male', 'origin' => 'Cibinong', 'domicile' => 'Cibinong', 'email' => null, 'phone' => '+6281286556500', 'start_date' => '2023-01-01', 'status' => 'active', 'created_by' => 2],
            ['user_id' => null, 'full_name' => 'Adi Kurniawan', 'given_name' => 'Adi', 'family_name' => 'Kurniawan', 'preferred_name' => 'Adi', 'birthday' => '1988-07-25', 'gender' => 'male', 'origin' => 'Cibinong', 'domicile' => 'Grand Wisata', 'email' => 'cibinongguy@gmail.com', 'phone' => '+6282177773968', 'start_date' => '2019-09-01', 'status' => 'active', 'created_by' => 2],
            ['user_id' => null, 'full_name' => 'Noveandi', 'given_name' => 'Noveandi', 'family_name' => null, 'preferred_name' => 'Andi', 'birthday' => '1990-01-01', 'gender' => 'male', 'origin' => 'Cibinong', 'domicile' => 'Karanggan', 'email' => null, 'phone' => '+628128636555', 'start_date' => '2023-01-01', 'status' => 'active', 'created_by' => 2],
            ['user_id' => null, 'full_name' => 'Edy Hermanto', 'given_name' => 'Edy', 'family_name' => 'Hermanto', 'preferred_name' => 'Edy', 'birthday' => '1990-01-01', 'gender' => 'male', 'origin' => 'Pacitan', 'domicile' => 'Karanggan', 'email' => null, 'phone' => '+6283131598990', 'start_date' => '2023-01-01', 'status' => 'active', 'created_by' => 2],
            ['user_id' => null, 'full_name' => 'Nung', 'given_name' => 'Nung', 'family_name' => 'Nung', 'preferred_name' => 'Nung', 'birthday' => '1990-01-01', 'gender' => 'female', 'origin' => 'Ciseeng', 'domicile' => 'Ciseeng', 'email' => null, 'phone' => '+6289603067362', 'start_date' => '2023-01-01', 'status' => 'active', 'created_by' => 2],
            ['user_id' => null, 'full_name' => 'Jumangat', 'given_name' => 'Jumangat', 'family_name' => 'Jumangat', 'preferred_name' => 'Jumangat', 'birthday' => '1990-01-01', 'gender' => 'male', 'origin' => 'Pacitan', 'domicile' => 'Ciseeng', 'email' => null, 'phone' => '+6289603067362', 'start_date' => '2023-01-01', 'status' => 'active', 'created_by' => 2],
        ];

        DB::beginTransaction();
        try {
            DB::table('employees')->insert($employees);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
        }
    }
}
