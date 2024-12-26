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
        Schema::create('spk_notas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spk_id')->nullable()->constrained()->onDelete('CASCADE');
            $table->foreignId('nota_id')->nullable()->constrained()->onDelete('CASCADE');
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
        Schema::dropIfExists('spk_notas');
    }
};
