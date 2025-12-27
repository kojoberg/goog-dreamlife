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
        // 1. Create Branches Table
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->nullable();
            $table->boolean('is_main')->default(false);
            $table->timestamps();
        });

        // 2. Insert Default 'Main Branch'
        $mainBranchId = DB::table('branches')->insertGetId([
            'name' => 'Main Branch',
            'location' => 'HQ',
            'is_main' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Add branch_id to Tables and Update Existing Records
        $tables = [
            'users',
            'inventory_batches',
            'sales',
            'purchase_orders',
            'expenses',
            'shifts'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                // Add column as nullable first to avoid constraint violation on creation if rows exist
                $table->foreignId('branch_id')->nullable()->after('id')->constrained('branches')->onDelete('set null');
            });

            // Update existing records to link to Main Branch
            DB::table($tableName)->update(['branch_id' => $mainBranchId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'users',
            'inventory_batches',
            'sales',
            'purchase_orders',
            'expenses',
            'shifts'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasColumn($tableName, 'branch_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropForeign(['branch_id']);
                    $table->dropColumn('branch_id');
                });
            }
        }

        Schema::dropIfExists('branches');
    }
};
