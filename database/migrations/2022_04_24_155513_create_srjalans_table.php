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
            $table->foreignId('pelanggan_id')->nullable()->constrained()->onDelete('SET NULL');
            $table->foreignId('ekspedisi_id')->nullable()->constrained()->onDelete('SET NULL');// constrained tetapi ketika ekspedisi dihapus, surat jalan janganlah dihapus
            $table->foreignId('ekspedisi_transit_id')->nullable()->constrained('ekspedisis','id')->onDelete('SET NULL');
            $table->bigInteger('reseller_id')->nullable();
            $table->foreignId('alamat_id')->nullable()->constrained()->onDelete('SET NULL'); // penting kalo sewaktu-waktu alamat utama pelanggan di edit.
            $table->foreignId('alamat_reseller_id')->nullable()->constrained('alamats','id')->onDelete('SET NULL'); // penting kalo sewaktu-waktu alamat utama pelanggan di edit.
            $table->foreignId('alamat_ekspedisi_id')->nullable()->constrained('alamats','id')->onDelete('SET NULL'); // penting kalo sewaktu-waktu alamat utama pelanggan di edit.
            $table->foreignId('alamat_transit_id')->nullable()->constrained('alamats','id')->onDelete('SET NULL'); // penting kalo sewaktu-waktu alamat utama pelanggan di edit.
            $table->foreignId('kontak_id')->nullable()->constrained('pelanggan_kontaks','id')->onDelete('SET NULL');
            $table->foreignId('kontak_reseller_id')->nullable()->constrained('pelanggan_kontaks','id')->onDelete('SET NULL');
            $table->foreignId('kontak_ekspedisi_id')->nullable()->constrained('ekspedisi_kontaks','id')->onDelete('SET NULL');
            $table->foreignId('kontak_transit_id')->nullable()->constrained('ekspedisi_kontaks','id')->onDelete('SET NULL');
            $table->string('status', 50)->default('PROSES KIRIM');
            // $table->smallInteger('jumlah')->nullable(); tidak perlu ada detail jumlah disini, karena sudah ada di spk_produk_nota_srjalan
            $table->smallInteger('jml_colly')->nullable();
            $table->smallInteger('jml_dus')->nullable();
            $table->smallInteger('jml_rol')->nullable();
            $table->string('jenis_barang',30)->nullable()->default('Sarung Jok Motor');
            $table->string('created_by');
            $table->string('updated_by');
            $table->timestamp('finished_at')->nullable();
            // Data ketika selesai
            $table->string('pelanggan_nama',100)->nullable();
            $table->string('cust_long_ala')->nullable();
            $table->string('cust_short')->nullable();
            $table->string('cust_kontak')->nullable();
            $table->string('ekspedisi_nama',100)->nullable();
            $table->string('eks_long_ala')->nullable();
            $table->string('eks_short')->nullable();
            $table->string('eks_kontak')->nullable();
            $table->string('transit_nama',100)->nullable();
            $table->string('trans_long_ala')->nullable();
            $table->string('trans_short')->nullable();
            $table->string('trans_kontak')->nullable();
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
        Schema::dropIfExists('srjalans');
    }
};
