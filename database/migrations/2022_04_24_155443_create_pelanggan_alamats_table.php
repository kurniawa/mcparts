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
        Schema::create('pelanggan_alamats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelanggan_id')->constrained()->onDelete('CASCADE');
            $table->foreignId('alamat_id')->constrained()->onDelete('CASCADE');
            $table->enum('tipe',['UTAMA','CADANGAN'])->default('CADANGAN');
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
        Schema::dropIfExists('pelanggan_alamats');
    }
};
