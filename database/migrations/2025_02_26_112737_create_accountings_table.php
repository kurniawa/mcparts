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
        Schema::create('accountings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('username', 100);
            $table->foreignId('user_instance_id')->nullable()->constrained()->onDelete('set null');
            $table->string('user_instance_type', 50);
            $table->string('user_instance_name', 50);
            $table->string('user_instance_branch', 50)->nullable();
            $table->string('account_number', 50)->nullable();
            $table->string('kode', 20);
            $table->string('transaction_type', 50);
            $table->string('transaction_desc');
            $table->string('kategori_type', 50)->nullable();
            $table->string('kategori_level_one', 100)->nullable();
            $table->string('kategori_level_two', 100)->nullable();
            $table->foreignId('related_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('related_username', 100)->nullable();
            $table->string('related_desc')->nullable();
            $table->foreignId('related_user_instance_id')->nullable()->constrained('user_instances')->onDelete('set null');
            $table->string('related_user_instance_type', 50)->nullable();
            $table->string('related_user_instance_name', 50)->nullable();
            $table->string('related_user_instance_branch', 50)->nullable();
            $table->foreignId('pelanggan_id')->nullable()->constrained()->onDelete('set null');
            $table->string('pelanggan_nama', 100)->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            $table->string('supplier_nama', 100)->nullable();
            $table->string('keterangan')->nullable();
            $table->decimal('jumlah', 20, 2);
            $table->decimal('saldo', 20, 2);
            $table->string('status', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accountings');
    }
};
