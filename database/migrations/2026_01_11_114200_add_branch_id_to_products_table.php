<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * Makes products branch-specific. Each branch has its own product catalog.
     */
    public function up(): void
    {
        // Add branch_id to products table
        if (!Schema::hasColumn('products', 'branch_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->after('id')->constrained('branches')->onDelete('set null');
            });
        }

        // Assign existing products to the main branch (if exists)
        $mainBranch = \App\Models\Branch::where('is_main', true)->first();
        if ($mainBranch) {
            \App\Models\Product::whereNull('branch_id')->update(['branch_id' => $mainBranch->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('products', 'branch_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }
    }
};
