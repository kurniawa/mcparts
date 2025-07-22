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
            $table->decimal('amount_paid', 15, 2)->after('status_bayar')->nullable();
            $table->decimal('amount_due', 15, 2)->after('amount_paid')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notas', function (Blueprint $table) {
            $table->dropColumn('amount_paid');
            $table->dropColumn('amount_due');
        });
    }
};
