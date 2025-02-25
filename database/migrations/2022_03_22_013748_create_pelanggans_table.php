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
        Schema::create('pelanggans', function (Blueprint $table) {
            $table->id();
            $table->string("tipe", 20)->nullable(); // pribadi, organisasi/badan/perusahaan
            $table->string("bentuk", 10)->nullable(); // PT, CV, Yayasan, Sekolah, dll.
            $table->string("nama", 100);
            $table->string("nama_organisasi", 100)->nullable();
            $table->string("nama_toko", 100)->nullable();
            $table->string("nama_pemilik", 100)->nullable();
            $table->enum("gender", ['pria', 'wanita'])->nullable();
            $table->string("nik", 50)->nullable();
            $table->string("alias", 100)->nullable();
            $table->string("sapaan", 10)->nullable();
            $table->string("gelar", 20)->nullable();
            $table->string("initial", 10)->nullable();
            $table->date("tanggal_lahir")->nullable();
            $table->string("kategori", 20)->nullable()->default('pelanggan'); // bisa juga dia tukang kredit misalnya
            $table->string("keterangan")->nullable();
            $table->boolean("is_reseller")->nullable()->default(false);
            $table->bigInteger("reseller_id")->nullable();
            $table->string('creator', 50)->nullable();
            $table->string('updater', 50)->nullable();
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
        Schema::dropIfExists('pelanggans');
    }
};
