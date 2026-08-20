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
        if (!Schema::hasTable('trade_unions')) {
            Schema::create('trade_unions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->string('union_name');
                $table->string('union_code')->nullable();
                $table->string('contact_person')->nullable();
                $table->string('contact_phone')->nullable();
                $table->string('contact_email')->nullable();
                $table->text('address')->nullable();
                $table->decimal('deduction_rate', 5, 2)->default(0); // Percentage of salary
                $table->decimal('fixed_amount', 10, 2)->default(0); // Fixed amount if applicable
                $table->enum('deduction_type', ['percentage', 'fixed'])->default('percentage');
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
                
                $table->index(['client_id', 'is_active']);
                $table->unique(['client_id', 'union_code']);
            });
        }

        if (!Schema::hasTable('employee_union_memberships')) {
            Schema::create('employee_union_memberships', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id');
                $table->unsignedBigInteger('employee_id');
                $table->unsignedBigInteger('trade_union_id');
                $table->date('membership_start_date');
                $table->date('membership_end_date')->nullable();
                $table->string('membership_number')->nullable();
                $table->enum('status', ['active', 'inactive', 'terminated'])->default('active');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
                $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
                $table->foreign('trade_union_id')->references('id')->on('trade_unions')->onDelete('cascade');
                
                $table->index(['client_id', 'employee_id']);
                $table->index(['client_id', 'trade_union_id']);
                $table->index(['client_id', 'status']);
            });
        }

        if (!Schema::hasTable('trade_union_deductions')) {
            Schema::create('trade_union_deductions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id');
                $table->unsignedBigInteger('employee_id');
                $table->unsignedBigInteger('trade_union_id');
                $table->string('payroll_period');
                $table->decimal('gross_salary', 10, 2)->default(0);
                $table->decimal('deduction_amount', 10, 2)->default(0);
                $table->date('deduction_date');
                $table->string('reference_number')->nullable();
                $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
                $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
                $table->foreign('trade_union_id')->references('id')->on('trade_unions')->onDelete('cascade');
                
                $table->index(['client_id', 'employee_id']);
                $table->index(['client_id', 'payroll_period']);
                $table->index(['client_id', 'status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trade_union_deductions');
        Schema::dropIfExists('employee_union_memberships');
        Schema::dropIfExists('trade_unions');
    }
};
