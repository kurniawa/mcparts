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
        Schema::create('spk_produk_selesais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spk_id')->constrained()->onDelete('CASCADE');
            $table->foreignId('spk_produk_id')->constrained()->onDelete('CASCADE');
            $table->smallInteger('jumlah')->nullable();
            $table->smallInteger('tahapan_selesai')->nullable();
            $table->timestamp('finished_at')->default(DB::raw('CURRENT_TIMESTAMP'));
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
        Schema::dropIfExists('spk_produk_selesais');
    }
};
