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
        // 1. Upgrade KPIs table
        Schema::table('kpis', function (Blueprint $table) {
            $table->decimal('weight', 5, 2)->default(10)->after('type'); // Default weight
            $table->string('category')->nullable()->after('weight'); // e.g. "Core", "Behavioral"
            $table->integer('max_score')->default(5)->after('weight'); // Standardizing on 5-point scale
        });

        // 2. Upgrade Appraisals table for Workflow
        Schema::table('appraisals', function (Blueprint $table) {
            $table->enum('status', ['draft', 'pending_employee', 'pending_manager', 'completed'])
                ->default('draft')
                ->after('period_month');
            $table->decimal('final_score', 5, 2)->nullable()->after('total_score');
        });

        // 3. Upgrade Appraisal Details for Self-Assessment
        Schema::table('appraisal_details', function (Blueprint $table) {
            $table->decimal('self_score', 5, 2)->nullable()->after('score');
            $table->text('self_comments')->nullable()->after('self_score');

            // Rename 'score' to 'manager_score' for clarity? 
            // Or just keep 'score' as the final/manager score. 
            // Let's keep 'score' as the manager's score to avoid breaking existing queries too much, 
            // but we'll treat it as such in the UI.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kpis', function (Blueprint $table) {
            $table->dropColumn(['weight', 'category', 'max_score']);
        });

        Schema::table('appraisals', function (Blueprint $table) {
            $table->dropColumn(['status', 'final_score']);
        });

        Schema::table('appraisal_details', function (Blueprint $table) {
            $table->dropColumn(['self_score', 'self_comments']);
        });
    }
};
