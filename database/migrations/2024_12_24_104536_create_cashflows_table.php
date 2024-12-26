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
        Schema::create('cashflows', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('nota_id')->nullable()->constrained()->onDelete('set null');
            $table->bigInteger('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('accounting_code')->nullable(); // kenapa tidak accounting_id? Karena bisa jadi 1 accounting memiliki 2 cashflow atau sebaliknya.
            $table->enum('type', ['pemasukan', 'pengeluaran'])->nullable(); // Bank
            $table->string('instance_type', 20)->nullable(); // Bank
            $table->string('instance_name', 20)->nullable(); // BCA
            $table->string('instance_branch', 20)->nullable(); // MC-Part's
            $table->decimal('payment_amount', 15, 2)->nullable();
            $table->decimal('balance', 15, 2)->nullable();
            $table->bigInteger('timekey')->nullable();
            $table->timestamps();
            // php artisan migrate --path=database/migrations/2024_12_24_104536_create_cashflows_table.php
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashflows');
    }
};
