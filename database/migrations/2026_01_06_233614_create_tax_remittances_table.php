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
        Schema::create('tax_remittances', function (Blueprint $table) {
            $table->id();
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('total_collected', 12, 2)->default(0);
            $table->decimal('total_remitted', 12, 2)->default(0);
            $table->json('tax_breakdown')->nullable(); // {NHIL: 500, VAT: 3000, ...}
            $table->enum('status', ['pending', 'partial', 'paid'])->default('pending');
            $table->date('remittance_date')->nullable();
            $table->string('reference_number')->nullable(); // GRA reference
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Who recorded
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_remittances');
    }
};
