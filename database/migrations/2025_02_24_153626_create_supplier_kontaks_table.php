<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('supplier_kontaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->onDelete('CASCADE');
            $table->string('tipe', 20)->nullable();
            $table->string('kodearea', 20)->nullable();
            $table->string('nomor', 20)->nullable();
            $table->boolean('is_actual')->nullable()->default(true);
            $table->string('lokasi', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_kontaks');
    }
};
