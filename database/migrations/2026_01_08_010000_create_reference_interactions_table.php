<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reference_interactions', function (Blueprint $table) {
            $table->id();
            $table->string('drug_a_name'); // Generic name A
            $table->string('drug_b_name'); // Generic name B
            $table->enum('severity', ['mild', 'moderate', 'severe'])->default('moderate');
            $table->text('description');
            $table->string('source')->nullable()->default('system_seed');
            $table->timestamps();

            $table->index('drug_a_name');
            $table->index('drug_b_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reference_interactions');
    }
};
