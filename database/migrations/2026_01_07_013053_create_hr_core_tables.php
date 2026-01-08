<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hr_core_tables', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->foreignId('head_of_department_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('employee_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('job_title')->nullable();
            $table->string('employment_status')->default('Full-time');
            $table->date('date_joined')->nullable();
            $table->decimal('basic_salary', 12, 2)->default(0);

            // Banking & Statutory
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('tin')->nullable();
            $table->string('ssnit_number')->nullable();
            $table->string('ghana_card_number')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_core_tables');
    }
};
