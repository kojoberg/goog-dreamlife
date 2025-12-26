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
            // Alert in days before expiry (default 90 days)
            $table->integer('alert_expiry_days')->default(90)->after('currency_symbol');

            // Note: Low Stock is usually per-product (reorder_level), but maybe a global default? 
            // The user asked "Low stock alert notice should be able to be set by user". 
            // We'll trust the per-product reorder_level for now, but maybe add a global override?
            // Let's stick to expiry days which is definitely global.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('alert_expiry_days');
        });
    }
};
