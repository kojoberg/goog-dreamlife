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
        Schema::table('patients', function (Blueprint $table) {
            $table->integer('loyalty_points')->default(0);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->integer('points_earned')->default(0);
            $table->integer('points_redeemed')->default(0);
        });

        Schema::table('settings', function (Blueprint $table) {
            // How much user spends to earn 1 point. Default: 10 GHS = 1 Point
            $table->decimal('loyalty_spend_per_point', 8, 2)->default(10.00);
            // How much 1 point is worth in GHS. Default: 1 Point = 0.20 GHS
            $table->decimal('loyalty_point_value', 8, 2)->default(0.20);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn('loyalty_points');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['points_earned', 'points_redeemed']);
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['loyalty_spend_per_point', 'loyalty_point_value']);
        });
    }
};
