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
        Schema::table('products', function (Blueprint $table) {
            $table->string('drug_route')->nullable()->after('description'); // e.g., Oral, IV
            $table->string('drug_form')->nullable()->after('drug_route');  // e.g., Tablet, Syrup
            $table->string('dosage')->nullable()->after('drug_form');      // e.g., 500mg
            $table->enum('product_type', ['goods', 'service'])->default('goods')->after('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['drug_route', 'drug_form', 'dosage', 'product_type']);
        });
    }
};
