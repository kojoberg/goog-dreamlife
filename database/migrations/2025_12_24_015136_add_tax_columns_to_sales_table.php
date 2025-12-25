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
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('subtotal', 10, 2)->after('patient_id')->default(0);
            $table->decimal('tax_amount', 10, 2)->after('subtotal')->default(0);
            $table->json('tax_breakdown')->after('tax_amount')->nullable(); // Stores {NHIL: x, GETFund: y, ...}
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['subtotal', 'tax_amount', 'tax_breakdown']);
        });
    }
};
