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
        Schema::create('notas', function (Blueprint $table) {
            $table->id();
            $table->string('no_nota', 20)->nullable();
            $table->foreignId('pelanggan_id')->nullable()->constrained('pelanggans')->onDelete('SET NULL');
            $table->foreignId('reseller_id')->nullable()->constrained('pelanggans')->onDelete('SET NULL');
            $table->string('status_bayar', 50)->default('BELUM');
            // $table->string('status_sj', 50)->default('BELUM');// Keliatannya sih tidak diperlukan
            // $table->integer('jumlah_sj')->nullable()->default(0);
            $table->integer('jumlah_total')->nullable();
            $table->integer('harga_total')->nullable();
            $table->foreignId('alamat_id')->nullable()->constrained()->onDelete('SET NULL'); // penting kalo sewaktu-waktu alamat utama pelanggan di edit.
            $table->foreignId('alamat_reseller_id')->nullable()->constrained('alamats','id')->onDelete('SET NULL'); // penting kalo sewaktu-waktu alamat utama pelanggan di edit.
            $table->foreignId('kontak_id')->nullable()->constrained('pelanggan_kontaks','id')->onDelete('SET NULL');
            $table->foreignId('kontak_reseller_id')->nullable()->constrained('pelanggan_kontaks','id')->onDelete('SET NULL');
            $table->string('created_by');
            $table->string('updated_by');
            $table->timestamp('finished_at')->nullable();
            // Data ketika selesai
            $table->string('pelanggan_nama',100)->nullable();
            $table->string('cust_long_ala')->nullable();
            $table->string('cust_short')->nullable();
            $table->string('cust_kontak')->nullable();
            $table->string('reseller_nama',100)->nullable();
            $table->string('reseller_long_ala')->nullable();
            $table->string('reseller_short')->nullable();
            $table->string('reseller_kontak')->nullable();
            // Keterangan Lain
            $table->string('keterangan')->nullable();
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
        Schema::dropIfExists('notas');
    }
};
