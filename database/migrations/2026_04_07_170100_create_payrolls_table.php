<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('payroll_period'); // e.g., "2024-11", "November 2024"
            $table->date('pay_date');
            $table->decimal('basic_salary', 10, 2);
            $table->decimal('overtime_hours', 5, 2)->default(0);
            $table->decimal('overtime_rate', 8, 2)->default(0);
            $table->decimal('overtime_pay', 10, 2)->default(0);
            $table->decimal('allowances', 10, 2)->default(0);
            $table->decimal('bonuses', 10, 2)->default(0);
            $table->decimal('gross_pay', 10, 2);
            $table->decimal('tax_deductions', 10, 2)->default(0);
            $table->decimal('social_security', 10, 2)->default(0);
            $table->decimal('pension', 10, 2)->default(0);
            $table->decimal('other_deductions', 10, 2)->default(0);
            $table->decimal('total_deductions', 10, 2)->default(0);
            $table->decimal('net_pay', 10, 2);
            $table->enum('status', ['draft', 'processed', 'paid', 'cancelled'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['client_id', 'employee_id']);
            $table->index(['client_id', 'payroll_period']);
            $table->index(['client_id', 'status']);
            $table->index(['pay_date']);
            $table->unique(['client_id', 'employee_id', 'payroll_period']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
