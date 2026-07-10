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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->uuid('uuid')->unique()->index();

            $table->string('employee_code', 20)->nullable()->unique();
            $table->string('employee_type', 20)->default('fulltime')->comment('permanent, fulltime, parttime, contract, daily, weekly, intern, freelancer');
            $table->foreignId('employee_type_id')->nullable()->constrained('employee_types')->nullOnDelete();

            $table->string('nationality', 50)->default('Indonesia');
            $table->string('id_type', 20)->comment('KTP, SIM, Passport, etc.');
            $table->string('id_number', 50);
            $table->string('full_name');
            $table->string('given_name')->nullable();
            $table->string('family_name', 100)->nullable();
            $table->string('preferred_name', 50)->nullable();

            $table->date('birthday');

            $table->enum('gender', [
                'male',
                'female',
            ]);

            $table->string('origin', 50)->nullable();
            $table->string('domicile', 50)->nullable();

            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();

            $table->string('photo')->nullable();
            $table->string('photo_id')->nullable();

            $table->date('start_date');

            $table->date('termination_date')->nullable();
            $table->text('reason_of_termination')->nullable();

            $table->enum('status', [
                'active',
                'inactive',
                'terminated',
                'resigned',
                'probation',
                'leave',
            ])->default('active');

            $table->text('notes')->nullable();

            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index('employee_code');
            $table->index('status');
            $table->index('start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
