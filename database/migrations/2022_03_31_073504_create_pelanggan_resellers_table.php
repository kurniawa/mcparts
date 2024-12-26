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
        Schema::create('pelanggan_resellers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reseller_id')->nullable()->constrained('pelanggans')->onDelete('SET NULL');
            $table->foreignId('pelanggan_id')->nullable()->constrained()->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pelanggan_resellers');
    }
};
