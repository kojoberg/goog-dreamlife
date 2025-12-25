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
        Schema::create('drug_interactions', function (Blueprint $table) {
            $table->id();
            // We use Foreign IDs for products. 
            // Unique constraint on pair to prevent duplicates?
            $table->foreignId('drug_a_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('drug_b_id')->constrained('products')->cascadeOnDelete();
            $table->enum('severity', ['mild', 'moderate', 'severe'])->default('moderate');
            $table->text('description');
            $table->timestamps();

            // Ensure unique pair (A, B)
            $table->unique(['drug_a_id', 'drug_b_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drug_interactions');
    }
};
