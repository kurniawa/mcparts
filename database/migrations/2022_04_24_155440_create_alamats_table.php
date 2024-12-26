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
        Schema::create('alamats', function (Blueprint $table) {
            $table->id();
            $table->string("jalan",100)->nullable(); // yang di Jakarta/Tangerang ada yang tidak pakai keterangan alamat. Ini bisa diisi dengan nama perumahan atau nama ruko, dll.
            $table->string("komplek", 100)->nullable(); // yang di Jakarta/Tangerang ada yang tidak pakai keterangan alamat. Ini bisa diisi dengan nama perumahan atau nama ruko, dll.
            $table->string("rt", 5)->nullable();
            $table->string("rw", 5)->nullable();
            $table->string("desa", 50)->nullable();
            $table->string("kelurahan", 50)->nullable();
            $table->string("kecamatan", 50)->nullable();
            $table->string("kota", 50)->nullable();
            $table->string("kodepos", 50)->nullable();
            $table->string("kabupaten", 50)->nullable();
            $table->string("provinsi", 50)->nullable();
            $table->string("pulau", 50)->nullable();
            $table->string("negara", 50)->nullable();
            $table->string("short", 50)->nullable();
            $table->string("long")->nullable(); // yang di Jakarta/Tangerang ada yang tidak pakai keterangan alamat. Ini bisa diisi dengan nama perumahan atau nama ruko, dll.
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
        Schema::dropIfExists('alamats');
    }
};
