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
            $table->string('pelanggan_nama',100)->nullable();
            $table->string('cust_long')->nullable();
            $table->foreignId('alamat_id')->nullable()->constrained()->onDelete('SET NULL'); // penting kalo sewaktu-waktu alamat utama pelanggan di edit.
            $table->string('cust_short')->nullable();
            $table->foreignId('kontak_id')->nullable()->constrained('pelanggan_kontaks','id')->onDelete('SET NULL');
            $table->string('cust_kontak')->nullable();
            $table->foreignId('reseller_id')->nullable()->constrained('pelanggans')->onDelete('SET NULL');
            $table->string('reseller_nama',100)->nullable();
            $table->string('reseller_long')->nullable();
            $table->string('reseller_short')->nullable();
            $table->foreignId('alamat_reseller_id')->nullable()->constrained('alamats','id')->onDelete('SET NULL'); // penting kalo sewaktu-waktu alamat utama pelanggan di edit.
            $table->foreignId('kontak_reseller_id')->nullable()->constrained('pelanggan_kontaks','id')->onDelete('SET NULL');
            $table->string('reseller_kontak')->nullable();
            $table->integer('jumlah_total')->nullable();
            $table->integer('harga_total')->nullable();
            $table->string('keterangan')->nullable();
            $table->string('status_bayar', 50)->default('BELUM_LUNAS');
            $table->decimal('discount_percent', 5, 2)->default(0.00);
            $table->decimal('discount_amount', 15, 2)->default(0.00);
            $table->decimal('other_discount', 15, 2)->default(0.00);
            $table->decimal('total_discount', 15, 2)->default(0.00);
            $table->string('discount_description')->nullable();
            $table->decimal('amount_due', 15, 2)->default(0.00);
            $table->decimal('amount_paid', 15, 2)->default(0.00);
            $table->decimal('balanced_used', 15, 2)->default(0.00);
            $table->decimal('overpayment', 15, 2)->default(0.00);
            $table->timestamps();
            $table->timestamp('finished_at')->nullable();
            $table->string('created_by');
            $table->string('updated_by');
            $table->boolean('copy')->nullable()->default(true);
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
