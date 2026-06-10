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
        Schema::table('pembelians', function (Blueprint $blueprint) {
            // Mengubah default nilai menjadi BELUM_LUNAS
            $blueprint->string('status_bayar')->default('BELUM_LUNAS')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembelians', function (Blueprint $blueprint) {
            // Kembalikan ke default sebelumnya jika migrasi di-rollback (misal: defaultnya null)
            $blueprint->string('status_bayar')->default('BELUM')->change();
        });
    }
};
