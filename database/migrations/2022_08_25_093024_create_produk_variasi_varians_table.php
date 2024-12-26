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
        Schema::create('produk_variasi_varians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained()->onDelete('CASCADE');
            $table->foreignId('variasi_id')->nullable()->constrained()->onDelete('SET NULL');
            $table->foreignId('varian_id')->nullable()->constrained()->onDelete('SET NULL');
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
        Schema::dropIfExists('produk_variasi_varians');
    }
};
