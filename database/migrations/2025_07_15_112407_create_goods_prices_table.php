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
        Schema::create('goods_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goods_id')->nullable()->constrained('barangs')->onDelete('set null');
            $table->string('goods_slug')->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            $table->string('supplier_name')->nullable();
            $table->tinyInteger('unit_order')->nullable();
            $table->string('unit', 50)->nullable();
            // $table->integer('amount')->nullable();
            $table->decimal('price', 15, 2)->default(0.00);
            // $table->decimal('total_price', 15, 2)->default(0.00);
            $table->string('price_type', 50)->nullable(); 
            $table->string('price_category', 50)->nullable(); 
            $table->string('price_order', 50)->nullable(); // primary, secondary, etc.

            $table->string('created_by', 50)->nullable(); // username of the user who created the record
            $table->string('updated_by', 50)->nullable();
            $table->timestamp('deleted_at')->nullable(); // Soft delete
            $table->string('deleted_by', 50)->nullable(); // User who deleted the record, if applicable
            $table->string('deleted_reason', 255)->nullable(); // Reason for deletion, if applicable
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_prices');
    }
};
