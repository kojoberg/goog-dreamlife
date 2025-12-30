<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->boolean('has_cashier')->default(false)->after('is_main');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->string('status')->default('completed')->after('payment_method');
        });

        // Backfill existing sales to completed (Redundant due to default, but explicit for safety)
        DB::table('sales')->update(['status' => 'completed']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn('has_cashier');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
