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
        Schema::create('overpayments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('accounting_id')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained('pelanggans')->onDelete('set null');
            $table->string('customer_name', 100)->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            $table->string('supplier_name', 100)->nullable();
            $table->decimal('amount', 15, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('overpayments');
    }
};
