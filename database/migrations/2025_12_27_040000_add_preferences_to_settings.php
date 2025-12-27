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
            $table->boolean('enable_tax')->default(true);
            $table->boolean('notify_low_stock_email')->default(false);
            $table->boolean('notify_low_stock_sms')->default(false);
            $table->boolean('notify_expiry_email')->default(false);
            $table->boolean('notify_expiry_sms')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'enable_tax',
                'notify_low_stock_email',
                'notify_low_stock_sms',
                'notify_expiry_email',
                'notify_expiry_sms'
            ]);
        });
    }
};
