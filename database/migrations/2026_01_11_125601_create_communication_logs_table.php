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
        Schema::create('communication_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['sms', 'email']);
            $table->string('recipient');
            $table->text('message')->nullable();
            $table->string('subject')->nullable(); // For emails
            $table->enum('status', ['sent', 'failed', 'pending'])->default('pending');
            $table->text('response')->nullable(); // API response
            $table->string('context')->nullable(); // e.g., "prescription_reminder", "test_sms"
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communication_logs');
    }
};
