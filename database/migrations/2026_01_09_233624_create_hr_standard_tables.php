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
        // Leave Types (Annual, Sick, Maternity, etc.)
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->integer('days_allowed')->default(15);
            $table->boolean('is_paid')->default(true);
            $table->boolean('requires_approval')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Leave Requests
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('leave_type_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('days_requested')->default(0);
            $table->string('status')->default('pending'); // pending, approved, rejected, cancelled
            $table->text('reason')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        // Work Shifts (Standard working hours)
        Schema::create('work_shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Regular Day", "Night Shift"
            $table->time('start_time');
            $table->time('end_time');
            $table->json('work_days')->nullable(); // ["Mon", "Tue", "Wed", "Thu", "Fri"]
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // HR Settings (Store generic key-value config if needed, or stick to Settings table)
        // We'll stick to the main Settings table for simple key-values to avoid bloat, 
        // but this table is good if we need complex structures. 
        // For now, let's keep it clean.
    }

    public function down(): void
    {
        Schema::dropIfExists('work_shifts');
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('leave_types');
    }
};
