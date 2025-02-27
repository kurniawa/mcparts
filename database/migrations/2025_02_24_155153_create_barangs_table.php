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
            $table->foreignId('supplier_id')->constrained()->onDelete('CASCADE');
            $table->string('supplier_nama', 50);
            $table->string('nama');
            $table->string('satuan_main', 20)->nullable();
            $table->string('satuan_sub', 20)->nullable();
            $table->decimal('harga_main', 15, 2)->nullable();
            $table->decimal('harga_sub', 15, 2)->nullable();
            $table->integer('jumlah_main')->nullable();
            $table->integer('jumlah_sub')->nullable();
            $table->decimal('harga_total_main', 20, 2)->nullable();
            $table->decimal('harga_total_sub', 20, 2)->nullable();
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
