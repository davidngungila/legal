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
        Schema::create('enhanced_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->string('employee_id', 50)->unique();
            $table->string('first_name', 255);
            $table->string('last_name', 255);
            $table->string('email', 255);
            $table->string('phone', 20);
            $table->enum('gender', ['male', 'female', 'other']);
            $table->date('date_of_birth');
            $table->string('national_id', 50)->unique();
            $table->string('passport_number', 50)->nullable();
            $table->string('tin_number', 30)->nullable();
            $table->string('nssf_number', 30)->nullable();
            $table->string('nhif_number', 30)->nullable();
            $table->string('position', 255);
            $table->string('department', 255);
            $table->foreignId('manager_id')->nullable()->constrained('enhanced_employees')->onDelete('set null');
            $table->date('hire_date');
            $table->date('termination_date')->nullable();
            $table->date('probation_end_date')->nullable();
            $table->enum('employment_type', ['permanent', 'contract', 'probation', 'intern', 'consultant', 'part_time', 'temporary']);
            $table->string('status', 50);
            $table->decimal('salary', 10, 2);
            $table->string('currency', 10)->default('TZS');
            $table->enum('payment_frequency', ['monthly', 'bi-weekly', 'weekly']);
            $table->string('bank_account', 50)->nullable();
            $table->string('bank_name', 255)->nullable();
            $table->string('bank_branch', 255)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('region', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('emergency_contact_name', 255)->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();
            $table->string('emergency_contact_relationship', 100)->nullable();
            $table->string('work_schedule', 255)->nullable();
            $table->string('reporting_to', 255)->nullable();
            $table->string('education_level', 255)->nullable();
            $table->json('professional_qualifications')->nullable();
            $table->json('certifications')->nullable();
            $table->json('skills')->nullable();
            $table->json('languages')->nullable();
            $table->string('profile_photo', 255)->nullable();
            $table->json('documents')->nullable();
            $table->integer('contracts_count')->default(0);
            $table->date('last_performance_review')->nullable();
            $table->date('next_performance_review')->nullable();
            $table->decimal('leave_balance', 8, 2)->default(0);
            $table->decimal('overtime_hours', 8, 2)->default(0);
            $table->json('benefits')->nullable();
            $table->json('tax_information')->nullable();
            $table->json('social_security_info')->nullable();
            $table->json('health_insurance_info')->nullable();
            $table->json('pension_info')->nullable();
            $table->foreignId('created_by')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained()->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['client_id', 'status']);
            $table->index(['client_id', 'department']);
            $table->index(['client_id', 'employment_type']);
            $table->index(['client_id', 'position']);
            $table->unique(['client_id', 'employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enhanced_employees');
    }
};
