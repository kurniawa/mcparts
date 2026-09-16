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
        Schema::table('barangs', function (Blueprint $table) {
            $table->foreignId('kategori_id')->after('id')->nullable()->constrained()->onDelete('set null');
            $table->string('kategori_nama', 100)->after('kategori_id')->nullable();
        });

        Schema::table('pembelians', function (Blueprint $table) {
            $table->foreignId('kategori_id')->after('id')->nullable()->constrained()->onDelete('set null');
            $table->string('kategori_nama', 100)->after('kategori_id')->nullable();
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->foreignId('kategori_id')->after('id')->nullable()->constrained()->onDelete('set null');
            $table->string('kategori_nama', 100)->after('kategori_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('kategori_id');
            $table->dropColumn('kategori_nama');
        });

        Schema::table('pembelians', function (Blueprint $table) {
            $table->dropConstrainedForeignId('kategori_id');
            $table->dropColumn('kategori_nama');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('kategori_id');
            $table->dropColumn('kategori_nama');
        });
    }
};
