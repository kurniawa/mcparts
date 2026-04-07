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
        Schema::create('pembelians', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_nota', 20)->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            $table->string('supplier_nama');
            $table->foreignId('supplier_alamat_id')->nullable()->constrained()->onDelete('set null');
            $table->string('supplier_long')->nullable();
            $table->string('supplier_short')->nullable();
            $table->foreignId('supplier_kontak_id')->nullable()->constrained()->onDelete('set null');
            $table->string('supplier_kontak')->nullable();
            $table->string('keterangan')->nullable();
            $table->string('isi')->nullable();
            $table->decimal('harga_total', 20, 2)->nullable();
            $table->string('status_bayar', 20)->default('BELUM_LUNAS'); // ['BELUM_LUNAS', 'SEBAGIAN', 'LUNAS']
            $table->string('keterangan_bayar')->nullable();
            $table->timestamp('tanggal_lunas')->nullable();

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
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelians');
    }
};
