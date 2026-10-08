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
        Schema::create('bilyet_giros', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('accounting_id')->nullable()->constrained('accountings')->nullOnDelete();
            $table->string('bilyet_number', 20)->unique();
            $table->foreignId('customer_id')->nullable()->constrained('pelanggans')->nullOnDelete();
            $table->string('customer_name', 255)->nullable();
            $table->string('issuer_name', 255);
            $table->string('issuer_bank', 100);
            $table->string('issuer_account_number', 50);
            $table->string('beneficiary_name', 255);
            $table->string('beneficiary_bank', 100);
            $table->string('beneficiary_account_number', 50);
            $table->decimal('amount', 15, 2);
            $table->date('received_date');
            $table->date('due_date');
            $table->date('clearing_date')->nullable();
            $table->enum('status', [
                'pending',
                'processing',
                'cleared',
                'rejected',
                'cancelled',
            ])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('cleared_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Index untuk pencarian berdasarkan jadwal kliring
            $table->index(['due_date', 'status']);
            $table->index(['clearing_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('bilyet_giros');
    }
};
