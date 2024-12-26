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
            // SJ-Variasi, SJ-Kombinasi, SJ-Japstyle,
            // SJ-Motif, SJ-Standar,SJ-T.Sixpack,
            // Jok Assy, Tankpad, Busa-Stang
            // Stiker,Rol,Rotan

            // $table->foreignId('bahan_id')->nullable();
            // $table->foreignId('variasi_id')->nullable();
            // $table->foreignId('varian_id')->nullable();
            // $table->foreignId('ukuran_id')->nullable();
            // $table->foreignId('jahit_id')->nullable();
            // $table->foreignId('kombi_id')->nullable();
            // $table->foreignId('tsixpack_id')->nullable();
            // $table->foreignId('japstyle_id')->nullable();
            // $table->foreignId('motif_id')->nullable();
            // $table->foreignId('standar_id')->nullable();
            // $table->foreignId('tankpad_id')->nullable();
            // $table->char('tipe_bahan', 1)->nullable();
            // $table->foreignId('stiker_id')->nullable();
            // $table->foreignId('busastang_id')->nullable();
            $table->string('nama');
            $table->string('nama_nota');
            $table->string('tipe_packing', 20)->nullable();
            $table->smallInteger('aturan_packing')->nullable();
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
