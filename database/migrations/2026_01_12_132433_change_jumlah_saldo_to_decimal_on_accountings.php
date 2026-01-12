<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('accountings', function (Blueprint $table) {
            $table->decimal('jumlah', 15, 2)->change();
            $table->decimal('saldo', 15, 2)->change();
        });

        // Konversi data lama (dibagi 100)
        DB::statement('
            UPDATE accountings
            SET 
                jumlah = jumlah,
                saldo = saldo
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke kondisi semula (kalikan 100)
        DB::statement('
            UPDATE accountings
            SET 
                jumlah = jumlah * 100,
                saldo = saldo * 100
        ');

        Schema::table('accountings', function (Blueprint $table) {
            $table->bigInteger('jumlah')->change();
            $table->bigInteger('saldo')->change();
        });
    }
};
