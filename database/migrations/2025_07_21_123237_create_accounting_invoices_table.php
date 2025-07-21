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
        Schema::table('accounting_invoices', function (Blueprint $table) {
            $table->foreignId('invoice_id')->nullable()->onDelete('set null');
            $table->string('invoice_table', 50)->nullable();
            $table->string('invoice_number', 50)->nullable();
            $table->string('payment_status', 50)->nullable(); // e.g., paid, unpaid, partial
            $table->string('payment_method', 50)->nullable(); // e.g., cash, bank transfer, credit card
            $table->decimal('amount_due', 15, 2)->default(0.00); // Amount still due for payment
            $table->decimal('amount_paid', 15, 2)->default(0.00); // Amount already paid
            $table->decimal('total_amount', 15, 2)->default(0.00); // Total amount of the invoice
            $table->date('due_date')->nullable(); // Due date for payment
            $table->string('currency', 10)->default('IDR'); // Currency of the invoice, defaulting to IDR
            $table->string('notes', 255)->nullable();
            $table->string('created_by', 50)->nullable(); // username of the user who created the record
            $table->string('updated_by', 50)->nullable();
            $table->timestamp('deleted_at')->nullable(); // Soft delete
            $table->string('deleted_by', 50)->nullable(); // User who deleted the record, if applicable
            $table->string('deleted_reason', 255)->nullable(); //
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_invoices');
    }
};
