<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        /**
         * Produks ini nanti nya akan berkaitan dengan table2 yang lain, meski tidak ada relasi yang dibuat pada table ini.
         * Tergantung dari tipe nya, semisal SJ-Variasi, berarti nantinya dia akan berkaitan dengan bahan, variasi, ukuran dan jahit.
         *
         */
        Schema::create('produks', function (Blueprint $table) {
            $table->id();
            $table->string('tipe', 50);
            $table->string('nama');
            $table->string('nama_nota');
            $table->string('tipe_packing', 20)->nullable();
            $table->smallInteger('aturan_packing')->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('SET NULL');
            $table->string('supplier_nama')->nullable();
            $table->string('keterangan')->nullable();
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
        Schema::dropIfExists('produks');
    }
};
