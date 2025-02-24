<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('username')->unique();
            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('account_type',20)->nullable();
            $table->string('user_type',20)->nullable();
            $table->string('customer_type',20)->nullable();
            $table->string('seller_status',20)->nullable();
            $table->enum('role',['developer','superadmin','admin','user'])->nullable()->default('user');
            $table->tinyInteger('clearance_level')->nullable()->default(0);
            $table->string('profile_picture')->nullable();
            $table->string('phone',20)->nullable();
            $table->string('position',50)->nullable();
            $table->string('company',50)->nullable();
            $table->string('address',100)->nullable();
            $table->string('city',50)->nullable();
            $table->string('province',50)->nullable();
            $table->string('country',50)->nullable();
            $table->string('postal_code',10)->nullable();
            $table->string('avatar',100)->nullable();
            $table->string('status',20)->nullable();
            $table->string('activation_code',100)->nullable();
            $table->timestamp('last_login')->nullable();
            $table->timestamp('last_logout')->nullable();
            $table->timestamp('last_activity')->nullable();
            $table->timestamp('last_password_reset')->nullable();
            $table->timestamp('last_password_change')->nullable();
            $table->timestamp('last_password_change_request')->nullable();
            $table->timestamp('last_password_change_reminder')->nullable();
            $table->timestamp('last_password_change_reminder_sent')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
