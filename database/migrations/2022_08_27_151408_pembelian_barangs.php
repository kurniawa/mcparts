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
            // $table->foreignId('supplier_id')->nullable(); // tidak butuh data ini, karena sudah ada pada table pembelians
            // $table->string('supplier_nama');
            $table->foreignId('barang_id')->nullable()->constrained()->onDelete('set null');
            $table->string('barang_nama');
            $table->string('satuan_main');
            $table->integer('jumlah_main');
            $table->bigInteger('harga_main');
            $table->string('satuan_sub')->nullable();
            $table->integer('jumlah_sub')->nullable();
            $table->bigInteger('harga_sub')->nullable();
            $table->bigInteger('harga_t');
            $table->string('keterangan')->nullable();
            $table->string('status_bayar', 20)->default('BELUM_LUNAS'); // ['BELUM_LUNAS', 'SEBAGIAN', 'LUNAS']
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
