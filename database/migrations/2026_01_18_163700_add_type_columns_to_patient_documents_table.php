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
        Schema::table('patient_documents', function (Blueprint $table) {
            $table->string('title')->nullable()->after('patient_id');
            $table->string('type')->nullable()->after('title'); // e.g., 'prescription_upload', 'lab_result'
            $table->text('notes')->nullable()->after('description');
            $table->foreignId('uploaded_by')->nullable()->after('notes')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_documents', function (Blueprint $table) {
            $table->dropForeign(['uploaded_by']);
            $table->dropColumn(['title', 'type', 'notes', 'uploaded_by']);
        });
    }
};
