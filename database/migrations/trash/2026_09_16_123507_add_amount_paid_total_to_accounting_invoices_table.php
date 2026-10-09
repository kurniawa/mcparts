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
            $table->decimal('amount_paid_total', 15, 2)->after('overpayment')->default(0.00);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounting_invoices', function (Blueprint $table) {
            $table->dropColumn('amount_paid_total');
        });
    }
};
