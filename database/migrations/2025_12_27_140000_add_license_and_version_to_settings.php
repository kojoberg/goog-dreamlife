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
            $table->string('license_key')->nullable()->after('tin_number');
            $table->date('license_expiry')->nullable()->after('license_key');
            $table->string('current_version')->default('1.0.0')->after('license_expiry');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['license_key', 'license_expiry', 'current_version']);
        });
    }
};
