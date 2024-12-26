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
        Schema::create('temp_spks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelanggan_id');
            $table->bigInteger('reseller_id')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('CASCADE');
            $table->string('judul')->nullable();
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
        Schema::dropIfExists('temp_spks');
    }
};
