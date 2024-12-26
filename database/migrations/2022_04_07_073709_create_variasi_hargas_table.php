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
        Schema::create('variasi_hargas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variasi_id')->constrained()->onDelete('CASCADE');
            $table->integer("harga");
            // Karena suka ditanya, ini harga dari kapan naik nya misalnya.
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
        Schema::dropIfExists('variasi_hargas');
    }
};
