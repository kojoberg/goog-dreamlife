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
            $table->text('license_key')->nullable()->change();
            $table->date('license_expiry')->nullable()->change(); // Ensure this is also nullable
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            // Revert is risky if nulls exist, but technically we would make it nullable(false).
            // For now, we leave it as valid.
        });
    }
};
