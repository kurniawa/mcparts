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
            // Data Pelanggan
            $table->foreignId('pelanggan_id')->nullable()->constrained('pelanggans')->onDelete('SET NULL');
            $table->string('pelanggan_nama',50)->nullable();
            $table->foreignId('alamat_id')->nullable()->constrained()->onDelete('SET NULL'); // penting kalo sewaktu-waktu alamat utama pelanggan di edit.
            $table->string('cust_long')->nullable();
            $table->string('cust_short')->nullable();
            $table->foreignId('kontak_id')->nullable()->constrained('pelanggan_kontaks','id')->onDelete('SET NULL');
            $table->string('cust_kontak')->nullable();
            
            // Data Reseller
            $table->foreignId('reseller_id')->nullable()->constrained('pelanggans')->onDelete('SET NULL');
            $table->string('reseller_nama',50)->nullable();
            $table->foreignId('reseller_alamat_id')->nullable()->constrained('alamats','id')->onDelete('SET NULL'); // penting kalo sewaktu-waktu alamat utama pelanggan di edit.
            $table->string('reseller_long')->nullable();
            $table->string('reseller_short')->nullable();
            $table->foreignId('reseller_kontak_id')->nullable()->constrained('pelanggan_kontaks','id')->onDelete('SET NULL');
            $table->string('reseller_kontak')->nullable();

            $table->integer('jumlah_total')->nullable();
            $table->integer('harga_total')->nullable();
            $table->string('status_bayar', 50)->default('BELUM');
            $table->boolean('copy')->nullable()->default(1);
            $table->string('created_by');
            $table->string('updated_by');
            $table->timestamp('finished_at')->nullable();
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
