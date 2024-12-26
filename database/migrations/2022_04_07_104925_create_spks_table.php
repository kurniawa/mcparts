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
        Schema::create('spks', function (Blueprint $table) {
            $table->id();
            $table->string('no_spk', 20)->nullable();
            $table->foreignId('pelanggan_id')->nullable()->constrained('pelanggans')->onDelete('SET NULL');
            $table->foreignId('reseller_id')->nullable()->constrained('pelanggans')->onDelete('SET NULL');
            $table->string('status', 20)->nullable()->default('PROSES');
            $table->string('status_nota', 20)->nullable()->default('BELUM');
            $table->string('status_sj', 20)->nullable()->default('BELUM');
            $table->string('status_tree', 20)->nullable()->default('BELUM');
            $table->string('judul')->nullable();
            // $table->text('data_spk_item');
            $table->integer('jumlah_selesai')->nullable()->default(0);
            $table->integer('jumlah_total')->nullable();
            $table->integer('harga_total')->nullable();
            $table->integer('jumlah_sudah_nota')->nullable()->default(0);
            $table->integer('jumlah_sudah_sj')->nullable()->default(0);
            // SPK Selesai: tanggal, pelanggan_nama, reseller_nama
            $table->string('pelanggan_nama',100)->nullable();
            $table->string('cust_long_ala')->nullable();
            $table->string('cust_short')->nullable();
            $table->string('cust_kontak')->nullable();
            $table->string('reseller_nama',100)->nullable();
            $table->string('reseller_long_ala')->nullable();
            $table->string('reseller_short')->nullable();
            $table->string('reseller_kontak')->nullable();
            $table->string('created_by');
            $table->string('updated_by');
            $table->timestamp('finished_at')->nullable();
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
        Schema::dropIfExists('spks');
    }
};
