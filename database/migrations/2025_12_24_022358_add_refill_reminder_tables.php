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
        if (!Schema::hasColumn('products', 'is_chronic')) {
            Schema::table('products', function (Blueprint $table) {
                $table->boolean('is_chronic')->default(false)->after('description');
            });
        }

        Schema::table('sale_items', function (Blueprint $table) {
            $table->integer('days_supply')->nullable();
        });

        Schema::create('refill_queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            // We reference sale_items to know which purchase triggered this
            $table->foreignId('sale_item_id')->constrained()->cascadeOnDelete();
            $table->string('product_name');
            $table->date('scheduled_date');
            $table->string('status')->default('pending'); // pending, sent, failed, cancelled
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refill_queues');

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn('days_supply');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('is_chronic');
        });
    }
};
