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
        Schema::table('pembelians', function (Blueprint $table) {
            $table->decimal('discount_percent', 5, 2)->default(0.00)->after('tanggal_lunas');
            $table->decimal('discount_amount', 15, 2)->default(0.00)->after('discount_percent');
            $table->decimal('other_discount', 15, 2)->default(0.00)->after('discount_amount');
            $table->decimal('total_discount', 15, 2)->default(0.00)->after('other_discount');
            $table->string('discount_description')->nullable()->after('total_discount');
            $table->decimal('amount_due', 15, 2)->default(0.00)->after('discount_description');
            $table->decimal('amount_paid', 15, 2)->default(0.00)->after('amount_due');
            $table->decimal('balanced_used', 15, 2)->default(0.00)->after('amount_paid');
            $table->decimal('overpayment', 15, 2)->default(0.00)->after('balanced_used');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembelians', function (Blueprint $table) {
            $table->dropColumn(['discount_percent', 'discount_amount', 'other_discount', 'total_discount', 'discount_description', 'amount_due', 'amount_paid', 'balanced_used', 'overpayment']);
        });
    }
};
