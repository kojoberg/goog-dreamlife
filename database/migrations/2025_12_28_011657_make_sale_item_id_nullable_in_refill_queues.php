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
        Schema::table('refill_queues', function (Blueprint $table) {
            $table->foreignId('sale_item_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('refill_queues', function (Blueprint $table) {
            $table->foreignId('sale_item_id')->nullable(false)->change();
        });
    }
};
