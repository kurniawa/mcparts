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
            $table->foreignId('user_instance_id')->after('accounting_id')->nullable()->constrained('user_instances')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounting_invoices', function (Blueprint $table) {
            $table->dropColumn('user_instance_id');
        });
    }
};
