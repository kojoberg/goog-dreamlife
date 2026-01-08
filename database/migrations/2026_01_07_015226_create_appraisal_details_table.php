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
        Schema::create('appraisal_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appraisal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('kpi_id')->constrained()->cascadeOnDelete();
            $table->decimal('score', 5, 2);
            $table->text('comments')->nullable();
            $table->unique(['appraisal_id', 'kpi_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appraisal_details');
    }
};
