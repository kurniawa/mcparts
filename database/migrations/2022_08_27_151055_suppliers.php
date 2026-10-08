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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->after('id')->nullable()->constrained()->onDelete('set null');
            $table->string('kategori_nama', 100)->after('kategori_id')->nullable();
            $table->string("bentuk", 10)->nullable(); // PT, CV, Yayasan, Sekolah, dll.
            $table->string("nama", 100);
            $table->string("nama_pemilik", 100)->nullable();
            $table->string("initial", 10)->nullable();
            $table->string("keterangan")->nullable();
            $table->string('creator', 50)->nullable();
            $table->string('updater', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
