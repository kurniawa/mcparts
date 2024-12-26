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
        Schema::create('pelanggan_ekspedisis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelanggan_id')->constrained()->onDelete('CASCADE');
            $table->foreignId('ekspedisi_id')->constrained()->onDelete('CASCADE');
            $table->enum('is_transit', ['yes','no'])->nullable()->default('no');
            $table->enum('tipe', ['UTAMA','CADANGAN'])->nullable()->default('UTAMA');
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
        Schema::dropIfExists('pelanggan_ekspedisis');
    }
};
