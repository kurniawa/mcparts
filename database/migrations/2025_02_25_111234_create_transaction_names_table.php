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
        Schema::create('transaction_names', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('username');
            $table->foreignId('user_instance_id')->nullable()->constrained()->onDelete('set null');
            $table->string('user_instance_type', 50);
            $table->string('user_instance_name', 50);
            $table->string('user_instance_branch', 50);
            $table->string('desc');
            $table->string('kategori_type', 100)->nullable();
            $table->string('kategori_level_one', 100)->nullable();
            $table->string('kategori_level_two', 100)->nullable();
            $table->foreignId('pelanggan_id')->nullable()->constrained()->onDelete('set null');
            $table->string('pelanggan_nama', 100)->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            $table->string('supplier_nama', 100)->nullable();
            $table->foreignId('related_user_id')->nullable()->constrained('users');
            $table->string('related_username', 100)->nullable();
            $table->string('related_desc')->nullable();
            $table->foreignId('related_user_instance_id')->nullable()->constrained('user_instances')->onDelete('set null');
            $table->string('related_user_instance_type', 50)->nullable();
            $table->string('related_user_instance_name', 50)->nullable();
            $table->string('related_user_instance_branch', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_names');
    }
};
