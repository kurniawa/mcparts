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
        Schema::create('spk_produks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spk_id')->constrained()->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreignId('produk_id')->nullable()->constrained()->onDelete('SET NULL');
            $table->string('nama_produk')->nullable(); // nama produk nullable mungkin karena tadinya memang tidak perlu diisi, tetapi nanti ke depannya untuk database aslinya diubah tidak nullable
            $table->string('keterangan',1000)->nullable();
            $table->smallInteger('jumlah');
            $table->smallInteger('deviasi_jumlah')->nullable()->default(0);
            $table->smallInteger('jumlah_total')->nullable()->default(0);
            $table->smallInteger('jumlah_selesai')->nullable()->default(0);
            // $table->smallInteger('jml_blm_sls')->nullable();
            $table->smallInteger('jumlah_sudah_nota')->nullable()->default(0);
            $table->smallInteger('jumlah_sudah_srjalan')->nullable()->default(0);
            $table->integer('koreksi_harga')->nullable();
            $table->string('status', 20)->nullable(); // Status yang berkaitan dengan sudah selesai di produksi atau belum
            // Ketika SPK Selesai: tanggal, nama_produk
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('spk_produks');
    }
};
