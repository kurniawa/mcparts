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
        Schema::create('user_instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('username');
            $table->foreignId('user_instance_id')->nullable()->constrained()->onDelete('set null');
            $table->string('instance_type', 50);
            $table->string('instance_name', 50);
            $table->string('instance_branch', 50);
            $table->string('account_number', 50)->nullable();
            $table->string('kode', 20)->nullable();
            $table->string('timerange', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_instances');
    }
};
