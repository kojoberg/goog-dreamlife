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
        Schema::table('settings', function (Blueprint $table) {
            $table->string('backup_schedule')->default('disabled'); // disabled, daily, weekly, monthly
            $table->string('backup_time')->default('02:00'); // Time of day for backup
            $table->integer('backup_day')->nullable(); // Day of week (0-6) or day of month (1-31)
            $table->integer('backup_retention_days')->default(30); // Auto-delete old backups
            $table->timestamp('last_backup_at')->nullable(); // Last successful backup time
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['backup_schedule', 'backup_time', 'backup_day', 'backup_retention_days', 'last_backup_at']);
        });
    }
};
