<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambahkan kolom time_key yang nullable dulu
        Schema::table('accountings', function (Blueprint $table) {
            $table->bigInteger('time_key')->after('status')->nullable();
        });

        // 2. Isi kolom time_key berdasarkan created_at (pakai raw SQL lebih cepat)
        Illuminate\Support\Facades\DB::statement("UPDATE accountings SET time_key = UNIX_TIMESTAMP(created_at)");

        // 3. Ubah kolom time_key menjadi NOT NULL
        // ->change() butuh doctrine/dbal
        Schema::table('accountings', function (Blueprint $table) {
            $table->bigInteger('time_key')->nullable(false)->change();
        });

        // 4. Tambahkan unique constraint
        Schema::table('accountings', function (Blueprint $table) {
            $table->unique('time_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accountings', function (Blueprint $table) {
            $table->dropUnique(['time_key']);
            $table->dropColumn('time_key');
        });
    }
};
