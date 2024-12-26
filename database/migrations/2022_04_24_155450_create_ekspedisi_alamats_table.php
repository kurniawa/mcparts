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
        Schema::create('ekspedisi_alamats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ekspedisi_id')->constrained()->onDelete('CASCADE');// hapus mah hapus aja, supaya ga menuh2 in database
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
        Schema::dropIfExists('ekspedisi_alamats');
    }
};
