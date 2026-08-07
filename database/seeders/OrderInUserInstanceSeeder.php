<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderInUserInstanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userInstances = \App\Models\UserInstance::all();

        DB::beginTransaction();
        try {
            foreach ($userInstances as $index => $userInstance) {
                if ($userInstance->kode === 'BCA MCP') {
                    $userInstance->parent = 'KAS BCA MCP';
                    $userInstance->order = 2;
                } elseif ($userInstance->kode === 'DNM MCP') {
                    $userInstance->parent = 'KAS DANAMON MCP';
                    $userInstance->order = 4;
                } elseif ($userInstance->kode === 'DNM DMD') {
                    $userInstance->parent = 'KAS DANAMON DMD';
                    $userInstance->order = 5;
                } elseif ($userInstance->kode === 'KTR 1') {
                    $userInstance->shown_name = 'KAS KANTOR ALBERT';
                    $userInstance->parent = 'KAS KANTOR';
                    $userInstance->order = 1;
                } elseif ($userInstance->kode === 'BCA DMD') {
                    $userInstance->parent = 'KAS BCA DMD';
                    $userInstance->order = 3;
                } elseif ($userInstance->kode === 'BRI DMD') {
                    $userInstance->parent = 'KAS BRI DMD';
                    $userInstance->order = 6;
                } elseif ($userInstance->kode === 'KTR AKHUN') {
                    $userInstance->shown_name = 'KAS KANTOR AKHUN';
                    $userInstance->parent = 'KAS KANTOR';
                    $userInstance->order = 1;
                } elseif ($userInstance->kode === 'KTR DIAN') {
                    $userInstance->shown_name = 'KAS KANTOR DIAN';
                    $userInstance->parent = 'KAS KANTOR';
                    $userInstance->order = 1;
                } elseif ($userInstance->kode === 'KTR DMD') {
                    $userInstance->shown_name = 'KAS KANTOR DMD';
                    $userInstance->parent = 'KAS KANTOR';
                    $userInstance->order = 1;
                } elseif ($userInstance->kode === 'BG') {
                    $userInstance->parent = 'GIRO DAN CEK';
                    $userInstance->order = 7;
                }
                $userInstance->save();
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
