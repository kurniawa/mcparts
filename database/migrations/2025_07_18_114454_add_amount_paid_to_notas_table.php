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
            $table->decimal('discount_percentage', 5, 2)->after('status_bayar')->nullable();
            $table->decimal('total_discount', 15, 2)->after('discount_percentage')->nullable();
            $table->decimal('discount_description', 15, 2)->after('total_discount')->nullable();
            $table->decimal('amount_paid', 15, 2)->after('discount_description')->nullable();
            $table->decimal('amount_due', 15, 2)->after('amount_paid')->nullable();
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
            $table->dropColumn('amount_paid');
            $table->dropColumn('amount_due');
        });
    }
};
