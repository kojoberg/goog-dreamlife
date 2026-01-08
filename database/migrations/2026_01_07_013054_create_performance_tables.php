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
        Schema::create('kpis', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('role')->nullable();
            $table->enum('type', ['quantitative', 'qualitative'])->default('quantitative');
            $table->timestamps();
        });

        Schema::create('staff_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('kpi_id')->constrained()->cascadeOnDelete();
            $table->decimal('target_value', 12, 2)->default(0);
            $table->string('period_month')->nullable(); // e.g. "2026-01"
            $table->timestamps();
        });

        Schema::create('appraisals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Staff
            $table->foreignId('reviewer_id')->nullable()->constrained('users'); // Manager
            $table->date('appraisal_date');
            $table->string('period_month'); // "2026-01"
            $table->decimal('total_score', 5, 2)->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_tables');
    }
};
