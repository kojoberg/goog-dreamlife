<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('appraisal_details', function (Blueprint $table) {
            if (!Schema::hasColumn('appraisal_details', 'appraisal_id')) {
                $table->foreignId('appraisal_id')->constrained()->cascadeOnDelete();
            }
            if (!Schema::hasColumn('appraisal_details', 'kpi_id')) {
                $table->foreignId('kpi_id')->constrained('kpis')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('appraisal_details', 'score')) {
                $table->decimal('score', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('appraisal_details', 'comments')) {
                $table->text('comments')->nullable();
            }

            // Re-add indices/unique constraints if needed, but be careful of duplicates
            // $table->unique(['appraisal_id', 'kpi_id']); 
            // We'll skip the unique constraint for now to avoid errors if logic handled in code, 
            // or we can try adding it safely.

        });

        // Add unique constraint separately to ensure columns exist first
        try {
            Schema::table('appraisal_details', function (Blueprint $table) {
                $table->unique(['appraisal_id', 'kpi_id']);
            });
        } catch (\Exception $e) {
            // Ignore if key exists
        }
    }

    public function down(): void
    {
        Schema::table('appraisal_details', function (Blueprint $table) {
            $table->dropColumn(['appraisal_id', 'kpi_id', 'score', 'comments']);
        });
    }
};
