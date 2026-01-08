<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'pharmacist', 'doctor', 'cashier', 'lab_scientist', 'patient') NOT NULL DEFAULT 'pharmacist'");
        }
    }

    public function down(): void
    {
        // Reverting this is tricky if there are users with 'patient' role.
        // For now, we leave it as is or revert to previous list if sure no data exists.
    }
};
