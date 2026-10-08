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
        Schema::table('user_instances', function (Blueprint $table) {
            $table->string('parent', 50)->after('kode')->nullable();
            $table->string('shown_name', 50)->after('parent')->nullable();
            $table->tinyInteger('order')->after('shown_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_instances', function (Blueprint $table) {
            $table->dropColumn('parent');
            $table->dropColumn('shown_name');
            $table->dropColumn('order');
        });
    }
};
