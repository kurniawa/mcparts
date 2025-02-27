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
        Schema::create('pembelian_barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembelian_id')->constrained()->onDelete('cascade');
            $table->foreignId('barang_id')->nullable()->constrained()->onDelete('set null');
            $table->string('barang_nama');
            $table->string('satuan_main', 20);
            $table->integer('jumlah_main');
            $table->decimal('harga_main', 20, 2);
            $table->string('satuan_sub', 20)->nullable();
            $table->integer('jumlah_sub')->nullable();
            $table->decimal('harga_sub', 20, 2)->nullable();
            $table->decimal('harga_t', 20, 2);
            $table->string('keterangan')->nullable();
            $table->string('status_bayar', 20)->default('BELUM');
            $table->string('keterangan_bayar')->nullable();
            $table->timestamp('tanggal_lunas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelian_barangs');
    }
};
