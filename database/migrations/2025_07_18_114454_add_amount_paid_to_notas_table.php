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
        Schema::table('notas', function (Blueprint $table) {
            $table->decimal('discount_percentage', 5, 2)->after('status_bayar')->default(0.00);
            $table->decimal('total_discount', 15, 2)->after('discount_percentage')->default(0.00);
            $table->string('discount_description')->after('total_discount')->nullable();
            $table->decimal('amount_due', 15, 2)->after('discount_description')->default(0.00);
            $table->decimal('amount_paid', 15, 2)->after('amount_due')->default(0.00);
            $table->decimal('balance_used', 15, 2)->after('amount_paid')->default(0.00);
            $table->decimal('overpayment', 15, 2)->after('balance_used')->default(0.00);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notas', function (Blueprint $table) {
            $table->dropColumn('discount_percentage');
            $table->dropColumn('total_discount');
            $table->dropColumn('discount_description');
            $table->dropColumn('amount_due');
            $table->dropColumn('amount_paid');
            $table->dropColumn('balance_used');
            $table->dropColumn('overpayment');
        });
    }
};
