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
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->nullable()->constrained()->onDelete('set null');
            $table->string('kategori_nama', 100)->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            $table->string('supplier_nama');
            $table->string('nama');
            $table->string('satuan_main')->nullable();
            $table->string('satuan_sub')->nullable();
            $table->decimal('harga_main', 15, 2)->nullable();
            $table->decimal('harga_sub', 15, 2)->nullable();
            $table->decimal('jumlah_main', 12, 2)->nullable();
            $table->decimal('jumlah_sub', 12, 2)->nullable();
            $table->decimal('harga_total_main', 15, 2)->nullable();
            $table->decimal('harga_total_sub', 15, 2)->nullable();
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
