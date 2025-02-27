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
        Schema::create('srjalans', function (Blueprint $table) {
            $table->id();
            $table->string('no_srjalan', 20)->nullable();
            // Data Pelanggan
            $table->foreignId('pelanggan_id')->nullable()->constrained()->onDelete('SET NULL');
            $table->string('pelanggan_nama',50)->nullable();
            $table->string('nama_tertera',50)->nullable();
            $table->foreignId('alamat_id')->nullable()->constrained()->onDelete('SET NULL'); // penting kalo sewaktu-waktu alamat utama pelanggan di edit.
            $table->string('cust_long')->nullable();
            $table->string('cust_short')->nullable();
            $table->foreignId('kontak_id')->nullable()->constrained('pelanggan_kontaks')->onDelete('SET NULL');
            $table->string('cust_kontak')->nullable();

            // Data Reseller
            $table->bigInteger('reseller_id')->nullable();
            $table->string('reseller_nama',50)->nullable();
            $table->foreignId('reseller_alamat_id')->nullable()->constrained('alamats')->onDelete('SET NULL'); // penting kalo sewaktu-waktu alamat utama pelanggan di edit.
            $table->string('reseller_long')->nullable();
            $table->string('reseller_short')->nullable();
            $table->foreignId('reseller_kontak_id')->nullable()->constrained('pelanggan_kontaks')->onDelete('SET NULL');
            $table->string('reseller_kontak')->nullable();

            // Data Ekspedisi
            $table->foreignId('ekspedisi_id')->nullable()->constrained()->onDelete('SET NULL');// constrained tetapi ketika ekspedisi dihapus, surat jalan janganlah dihapus
            $table->string('ekspedisi_nama',50)->nullable();
            $table->foreignId('ekspedisi_alamat_id')->nullable()->constrained('alamats')->onDelete('SET NULL'); // penting kalo sewaktu-waktu alamat utama pelanggan di edit.
            $table->string('ekspedisi_long')->nullable();
            $table->string('ekspedisi_short')->nullable();
            $table->foreignId('ekspedisi_kontak_id')->nullable()->constrained('ekspedisi_kontaks')->onDelete('SET NULL');
            $table->string('ekspedisi_kontak')->nullable();

            // Data Transit
            $table->foreignId('ekspedisi_transit_id')->nullable()->constrained('ekspedisis')->onDelete('SET NULL');
            $table->string('transit_nama',50)->nullable();
            $table->foreignId('transit_alamat_id')->nullable()->constrained('alamats')->onDelete('SET NULL'); // penting kalo sewaktu-waktu alamat utama pelanggan di edit.
            $table->string('transit_long')->nullable();
            $table->string('transit_short')->nullable();
            $table->foreignId('transit_kontak_id')->nullable()->constrained('ekspedisi_kontaks')->onDelete('SET NULL');
            $table->string('transit_kontak')->nullable();
            
            $table->string('status', 50)->default('PROSES KIRIM');
            $table->smallInteger('jumlah_colly')->nullable();
            $table->smallInteger('jumlah_dus')->nullable();
            $table->smallInteger('jumlah_rol')->nullable();
            $table->string('jumlah_packing', 200)->nullable();
            $table->string('jenis_barang',30)->nullable()->default('Sarung Jok Motor');
            $table->string('created_by');
            $table->string('updated_by');
            $table->timestamp('finished_at')->nullable();
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
        Schema::dropIfExists('srjalans');
    }
};
