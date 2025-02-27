<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spk_produk_notas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spk_id')->nullable()->constrained()->onDelete('CASCADE');
            $table->foreignId('produk_id')->nullable()->constrained()->onDelete('SET NULL');
            $table->foreignId('spk_produk_id')->nullable()->constrained()->onDelete('SET NULL');
            $table->foreignId('nota_id')->nullable()->constrained()->onDelete('CASCADE');
            $table->foreignId('pelanggan_id')->nullable()->constrained()->onDelete('CASCADE');
            $table->smallInteger('jumlah');
            $table->foreignId('produk_harga_id')->nullable()->constrained()->onDelete('SET NULL'); // Ini perlu untuk acuan dalam pengeditan harga nantinya
            $table->foreignId('pelanggan_produk_id')->nullable()->constrained()->onDelete('SET NULL');
            $table->foreignId('namaproduk_id')->nullable()->constrained('pelanggan_namaproduks')->onDelete('SET NULL');
            $table->boolean('is_price_updated')->nullable()->default(false);
            // Nota Selesai: Acuan nya ternyata sudah harga dan harga_t dibawah ini:
            $table->string('nama_nota')->nullable();
            $table->decimal('harga', 15, 2);
            $table->decimal('harga_t', 20, 2);
            $table->string('keterangan', 1000)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('spk_produk_notas');
    }
};
