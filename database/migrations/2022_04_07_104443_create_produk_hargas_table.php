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
        Schema::create('produk_hargas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained()->onDelete('CASCADE');
            $table->integer('harga');
            $table->enum('status',['DEFAULT','BARU','LAMA'])->nullable()->default('LAMA'); // Kalau tidak ada harga baru, maka harga default dianggap juga harga terbaru.
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
        Schema::dropIfExists('produk_hargas');
    }
};
