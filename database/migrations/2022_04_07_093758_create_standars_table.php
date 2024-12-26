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
         * Kulit Jok Standar memiliki beberapa atribut diantaranya:
         * jht_samping, press, alas
         * Masing2 attribut memiliki harga tersendiri, oleh karena itu akan dibuat 2 tabel tambahan
         * tabel std_harga dimana kolom keterangan jht_samping, press, alas
         * tabel (tambahan, berupa string bisa berisi jht_samping, press atau alas) dan kolom harga
         */
        Schema::create('standars', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->integer('harga_dasar');
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
        Schema::dropIfExists('standars');
    }
};
