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
        Schema::create('spk_produk_nota_srjalans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spk_id')->constrained()->onDelete('CASCADE');
            $table->foreignId('produk_id')->nullable()->constrained()->onDelete('SET NULL');
            $table->foreignId('nota_id')->constrained()->onDelete('CASCADE');
            $table->foreignId('srjalan_id')->constrained()->onDelete('CASCADE');
            $table->foreignId('spk_produk_id')->nullable()->constrained()->onDelete('SET NULL');
            $table->foreignId('spk_produk_nota_id')->nullable()->constrained()->onDelete('SET NULL');
            $table->string('tipe_packing', 20)->nullable();
            // Penetapan Data Sr. Jalan Selesai:
            $table->smallInteger('jumlah');
            $table->smallInteger('jml_packing')->nullable();
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
        Schema::dropIfExists('spk_produk_nota_srjalans');
    }
};
