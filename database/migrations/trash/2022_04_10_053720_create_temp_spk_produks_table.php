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
        Schema::create('temp_spk_produks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id');
            $table->smallInteger('jumlah');
            $table->foreignId('temp_spk_id')->constrained()->onDelete('CASCADE');
            $table->string('keterangan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('temp_spk_produks');
    }
};
