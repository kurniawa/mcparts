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
        Schema::create('pembelians', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_nota', 20)->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            $table->string('supplier_nama', 100);
            $table->foreignId('supplier_alamat_id')->nullable()->constrained()->onDelete('set null');
            $table->string('supplier_long')->nullable();
            $table->string('supplier_short')->nullable();
            $table->foreignId('supplier_kontak_id')->nullable()->constrained('supplier_kontaks')->onDelete('set null');
            $table->string('supplier_kontak', 50)->nullable();
            $table->string('keterangan')->nullable();
            $table->string('isi')->nullable();
            $table->decimal('harga_total', 15, 2)->nullable();
            $table->string('status_bayar', 20)->default('BELUM');
            $table->string('keterangan_bayar')->nullable();
            $table->timestamp('tanggal_lunas')->nullable();
            $table->string('creator', 50)->nullable();
            $table->string('updater', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelians');
    }
};
