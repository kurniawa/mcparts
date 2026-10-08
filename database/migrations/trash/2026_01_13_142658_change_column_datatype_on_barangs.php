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
        Schema::table('barangs', function (Blueprint $table) {
            $table->decimal('jumlah_main', 12, 2)->nullable()->change();
            $table->decimal('jumlah_sub', 12, 2)->nullable()->change();
            $table->decimal('harga_total_main', 15, 2)->nullable()->change();
            $table->decimal('harga_total_sub', 15, 2)->nullable()->change();
        });

        // Konversi data lama (dibagi 100)
        DB::statement('
            UPDATE barangs
            SET 
                jumlah_main = jumlah_main / 100,
                jumlah_sub = jumlah_sub / 100
        ');

        Schema::table('pembelian_barangs', function (Blueprint $table) {
            $table->decimal('jumlah_main', 12, 2)->nullable()->change();
            $table->decimal('jumlah_sub', 12, 2)->nullable()->change();
        });

        // Konversi data lama (dibagi 100)
        DB::statement('
            UPDATE pembelian_barangs
            SET 
                jumlah_main = jumlah_main / 100,
                jumlah_sub = jumlah_sub / 100
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke kondisi semula (kalikan 100)
        DB::statement('
            UPDATE barangs
            SET 
                jumlah_main = jumlah_main * 100,
                jumlah_sub = jumlah_sub * 100
        ');

        Schema::table('barangs', function (Blueprint $table) {
            $table->integer('jumlah_main')->nullable()->change();
            $table->integer('jumlah_sub')->nullable()->change();
            $table->integer('harga_total_main')->nullable()->change();
            $table->bigInteger('harga_total_sub')->nullable()->change();
        });

        DB::statement('
            UPDATE pembelian_barangs
            SET 
                jumlah_main = jumlah_main * 100,
                jumlah_sub = jumlah_sub * 100
        ');

        Schema::table('pembelian_barangs', function (Blueprint $table) {
            $table->integer('jumlah_main')->nullable()->change();
            $table->integer('jumlah_sub')->nullable()->change();
        });
    }
};
