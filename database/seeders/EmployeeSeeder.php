<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\EmployeeType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employee_types = [
            ['code' => 'PERMANENT', 'name' => 'Tetap', 'description' => 'Permanent Employee', 'sort_order' => 1, 'created_by' => 2],
            ['code' => 'CONTRACT', 'name' => 'Kontrak', 'description' => 'Contract Employee', 'sort_order' => 2, 'created_by' => 2],
            ['code' => 'DAILY', 'name' => 'Harian', 'description' => 'Daily Employee', 'sort_order' => 3, 'created_by' => 2],
            ['code' => 'WEEKLY', 'name' => 'Mingguan', 'description' => 'Weekly Employee', 'sort_order' => 4, 'created_by' => 2],
            ['code' => 'FULLTIME', 'name' => 'Penuh Waktu', 'description' => 'Full-time Employee', 'sort_order' => 5, 'created_by' => 2],
            ['code' => 'PARTTIME', 'name' => 'Paruh Waktu', 'description' => 'Part-time Employee', 'sort_order' => 6, 'created_by' => 2],
            ['code' => 'INTERN', 'name' => 'Magang', 'description' => 'Intern Employee', 'sort_order' => 7, 'created_by' => 2],
            ['code' => 'FREELANCER', 'name' => 'Freelancer', 'description' => 'Freelancer Employee', 'sort_order' => 8, 'created_by' => 2],
        ];

        $employees = [
            ['user_id' => null, 'full_name' => 'Demardi', 'given_name' => 'Demardi', 'family_name' => null, 'preferred_name' => 'Demardi', 'employee_type' => 'PERMANENT', 'birthday' => '1990-01-01', 'gender' => 'male', 'origin' => 'Jakarta', 'domicile' => 'Karanggan', 'email' => null, 'phone' => '+628129335218', 'start_date' => '2023-01-01', 'status' => 'active', 'created_by' => 2],
            ['user_id' => null, 'full_name' => 'Albert', 'given_name' => 'Albert', 'family_name' => null, 'preferred_name' => 'Albert', 'employee_type' => 'PERMANENT', 'birthday' => '1990-01-01', 'gender' => 'male', 'origin' => 'Cibinong', 'domicile' => 'Cibinong', 'email' => null, 'phone' => '+6281286556500', 'start_date' => '2023-01-01', 'status' => 'active', 'created_by' => 2],
            ['user_id' => null, 'full_name' => 'Lie Dian Putra', 'given_name' => 'Dian', 'family_name' => 'Lie', 'preferred_name' => 'Dian', 'employee_type' => 'PERMANENT', 'birthday' => '1990-01-01', 'gender' => 'male', 'origin' => 'Cibinong', 'domicile' => 'Cibinong', 'email' => null, 'phone' => '+6281286556500', 'start_date' => '2023-01-01', 'status' => 'active', 'created_by' => 2],
            ['user_id' => null, 'full_name' => 'Adi Kurniawan', 'given_name' => 'Adi', 'family_name' => 'Kurniawan', 'preferred_name' => 'Adi', 'employee_type' => 'PERMANENT', 'birthday' => '1988-07-25', 'gender' => 'male', 'origin' => 'Cibinong', 'domicile' => 'Grand Wisata', 'email' => 'cibinongguy@gmail.com', 'phone' => '+6282177773968', 'start_date' => '2019-09-01', 'status' => 'active', 'created_by' => 2],
            ['user_id' => null, 'full_name' => 'Noveandi', 'given_name' => 'Noveandi', 'family_name' => null, 'preferred_name' => 'Andi', 'employee_type' => 'PERMANENT', 'birthday' => '1990-01-01', 'gender' => 'male', 'origin' => 'Cibinong', 'domicile' => 'Karanggan', 'email' => null, 'phone' => '+628128636555', 'start_date' => '2023-01-01', 'status' => 'active', 'created_by' => 2],
            ['user_id' => null, 'full_name' => 'Edy Hermanto', 'given_name' => 'Edy', 'family_name' => 'Hermanto', 'preferred_name' => 'Edy', 'employee_type' => 'PERMANENT', 'birthday' => '1990-01-01', 'gender' => 'male', 'origin' => 'Pacitan', 'domicile' => 'Karanggan', 'email' => null, 'phone' => '+6283131598990', 'start_date' => '2023-01-01', 'status' => 'active', 'created_by' => 2],
            ['user_id' => null, 'full_name' => 'Nung', 'given_name' => 'Nung', 'family_name' => 'Nung', 'preferred_name' => 'Nung', 'employee_type' => 'PERMANENT', 'birthday' => '1990-01-01', 'gender' => 'female', 'origin' => 'Ciseeng', 'domicile' => 'Ciseeng', 'email' => null, 'phone' => '+6289603067362', 'start_date' => '2023-01-01', 'status' => 'active', 'created_by' => 2],
            ['user_id' => null, 'full_name' => 'Jumangat', 'given_name' => 'Jumangat', 'family_name' => 'Jumangat', 'preferred_name' => 'Jumangat', 'employee_type' => 'PERMANENT', 'birthday' => '1990-01-01', 'gender' => 'male', 'origin' => 'Pacitan', 'domicile' => 'Karanggan', 'email' => null, 'phone' => '+6289603067362', 'start_date' => '2023-01-01', 'status' => 'active', 'created_by' => 2],
            ['user_id' => null, 'full_name' => 'Misno', 'given_name' => 'Misno', 'family_name' => 'Misno', 'preferred_name' => 'Misno', 'employee_type' => 'PERMANENT', 'birthday' => '1990-01-01', 'gender' => 'male', 'origin' => 'Pacitan', 'domicile' => 'Karanggan', 'email' => null, 'phone' => '+6289603067362', 'start_date' => '2023-01-01', 'status' => 'active', 'created_by' => 2],
            ['user_id' => null, 'full_name' => 'Gini', 'given_name' => 'Gini', 'family_name' => 'Gini', 'preferred_name' => 'Gini', 'employee_type' => 'PERMANENT', 'birthday' => '1990-01-01', 'gender' => 'male', 'origin' => 'Pacitan', 'domicile' => 'Karanggan', 'email' => null, 'phone' => '+6289603067362', 'start_date' => '2023-01-01', 'status' => 'active', 'created_by' => 2],
            ['user_id' => null, 'full_name' => 'Okta', 'given_name' => 'Okta', 'family_name' => 'Okta', 'preferred_name' => 'Okta', 'employee_type' => 'PERMANENT', 'birthday' => '1990-01-01', 'gender' => 'female', 'origin' => 'Karanggan', 'domicile' => 'Karanggan', 'email' => null, 'phone' => '+6289603067362', 'start_date' => '2023-01-01', 'status' => 'active', 'created_by' => 2],
        ];

        DB::beginTransaction();
        try {
            foreach ($employee_types as $employee_type) {
                EmployeeType::create($employee_type);
            }

            foreach ($employees as $employee) {
                $new_employee =Employee::create($employee);
                $employee_type = EmployeeType::where('code', $employee['employee_type'])->first();
                $new_employee->update([
                    'employee_code' => 'EMP' . str_pad($new_employee->id, 6, '0', STR_PAD_LEFT),
                    'employee_type_id' => $employee_type->id,
                ]);
            }

            DB::commit();
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            DB::rollBack();
        }
    }
}
