<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('employment_contracts');

        Schema::create('employment_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained('clients')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('contract_number')->nullable();
            $table->string('contract_title')->nullable();
            $table->string('contract_type')->default('unspecified');
            $table->date('effective_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->date('probation_end_date')->nullable();
            $table->string('job_title')->nullable();
            $table->string('department')->nullable();
            $table->string('reporting_line')->nullable();
            $table->string('work_location')->nullable();
            $table->string('work_schedule')->nullable();
            $table->string('salary_currency', 3)->default('TZS');
            $table->decimal('basic_salary', 14, 2)->default(0);
            $table->decimal('housing_allowance', 14, 2)->default(0);
            $table->decimal('transport_allowance', 14, 2)->default(0);
            $table->decimal('meal_allowance', 14, 2)->default(0);
            $table->decimal('other_allowances', 14, 2)->default(0);
            $table->decimal('total_compensation', 14, 2)->default(0);
            $table->string('payment_frequency')->default('monthly');
            $table->string('payment_method')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->decimal('working_hours_per_week', 5, 1)->nullable();
            $table->decimal('overtime_rate', 4, 2)->nullable();
            $table->integer('leave_entitlement_days')->default(0);
            $table->integer('sick_leave_days')->default(0);
            $table->integer('public_holidays')->default(0);
            $table->integer('maternity_leave_weeks')->default(0);
            $table->integer('paternity_leave_weeks')->default(0);
            $table->integer('notice_period_days')->default(30);
            $table->boolean('confidentiality_clause')->default(false);
            $table->boolean('non_compete_clause')->default(false);
            $table->integer('non_compete_duration_months')->nullable();
            $table->text('non_compete_restriction')->nullable();
            $table->boolean('intellectual_property_clause')->default(false);
            $table->boolean('data_protection_clause')->default(false);
            $table->boolean('health_and_safety_clause')->default(false);
            $table->boolean('training_development_clause')->default(false);
            $table->boolean('company_policies_acknowledgment')->default(false);
            $table->text('termination_clause')->nullable();
            $table->text('grievance_procedure')->nullable();
            $table->text('disciplinary_procedure')->nullable();
            $table->text('benefits_package')->nullable();
            $table->string('performance_review_frequency')->nullable();
            $table->string('contract_document_path')->nullable();
            $table->string('signed_contract_path')->nullable();
            $table->string('witness_name')->nullable();
            $table->string('witness_title')->nullable();
            $table->string('witness_signature_path')->nullable();
            $table->string('status')->default('draft');
            $table->text('notes')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('terminated_at')->nullable();
            $table->string('termination_reason')->nullable();
            $table->string('termination_type')->nullable();
            $table->integer('renewal_count')->default(0);
            $table->date('last_renewal_date')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index(['client_id', 'status']);
            $table->index(['employee_id', 'status']);
            $table->index('expiry_date');
            $table->unique(['client_id', 'contract_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employment_contracts');
    }
};
