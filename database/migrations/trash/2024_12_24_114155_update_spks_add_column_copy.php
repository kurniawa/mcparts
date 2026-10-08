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
        Schema::table('spks', function (Blueprint $table) {
            $table->boolean('copy')->nullable()->default(1)->after('status_srjalan');
            // php artisan migrate --path=database/migrations/2024_12_24_114155_update_nota_add_column_copy.php
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spks', function (Blueprint $table) {
            $table->dropColumn('copy');
        });
    }
};
