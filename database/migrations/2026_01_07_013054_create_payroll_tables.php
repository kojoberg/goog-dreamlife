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
        Schema::create('tax_bands', function (Blueprint $table) {
            $table->id();
            $table->decimal('band_width', 12, 2)->nullable(); // Null = Excess
            $table->decimal('tax_rate', 5, 2); // %
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('month_year'); // "2026-01"

            $table->decimal('basic_salary', 12, 2);
            $table->decimal('total_allowances', 12, 2)->default(0);
            $table->decimal('gross_salary', 12, 2);

            // Deductions
            $table->decimal('tier_1', 10, 2)->default(0); // Employer SSNIT 13%
            $table->decimal('tier_2', 10, 2)->default(0); // Employee SSNIT 5.5% 
            $table->decimal('tier_3', 10, 2)->default(0); // Provident
            $table->decimal('paye_tax', 12, 2)->default(0);
            $table->decimal('other_deductions', 12, 2)->default(0);

            $table->decimal('net_salary', 12, 2);
            $table->string('status')->default('Draft'); // Draft, Approved, Paid
            $table->timestamps();
        });

        Schema::create('payroll_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('amount', 12, 2);
            $table->enum('type', ['allowance', 'deduction', 'bonus']);
            $table->boolean('is_taxable')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_tables');
    }
};
