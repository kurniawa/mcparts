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
        Schema::create('standar_variasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('standar_id')->constrained()->onDelete('CASCADE');
            $table->foreignId('bahan_id')->nullable();
            $table->enum('grade_bahan', ['A', 'B'])->nullable();
            $table->enum('jahit_kepala', ['yes', 'no'])->default('yes');
            $table->enum('jahit_samping', ['yes', 'no'])->nullable();
            $table->string('warna_sayap', 20)->nullable();
            $table->enum('press', ['yes', 'no'])->nullable();
            $table->enum('alas', ['yes', 'no'])->nullable();
            $table->integer('harga');
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
        Schema::dropIfExists('standar_variasi_hargas');
    }
};
