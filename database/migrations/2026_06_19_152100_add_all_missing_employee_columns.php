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
        Schema::table('employees', function (Blueprint $table) {
            // Personal info
            if (!Schema::hasColumn('employees', 'phone')) {
                $table->string('phone', 20)->nullable()->after('email');
            }
            if (!Schema::hasColumn('employees', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('employees', 'gender')) {
                $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('date_of_birth');
            }
            
            // Legal IDs
            if (!Schema::hasColumn('employees', 'national_id')) {
                $table->string('national_id', 50)->nullable()->after('gender');
            }
            if (!Schema::hasColumn('employees', 'passport_number')) {
                $table->string('passport_number', 50)->nullable()->after('national_id');
            }
            if (!Schema::hasColumn('employees', 'tin_number')) {
                $table->string('tin_number', 30)->nullable()->after('passport_number');
            }
            if (!Schema::hasColumn('employees', 'nssf_number')) {
                $table->string('nssf_number', 30)->nullable()->after('tin_number');
            }
            if (!Schema::hasColumn('employees', 'nhif_number')) {
                $table->string('nhif_number', 30)->nullable()->after('nssf_number');
            }
            
            // Manager
            if (!Schema::hasColumn('employees', 'manager_id')) {
                $table->unsignedBigInteger('manager_id')->nullable()->after('department');
            }
            
            // Bank details
            if (!Schema::hasColumn('employees', 'bank_account')) {
                $table->string('bank_account', 50)->nullable()->after('payment_frequency');
            }
            if (!Schema::hasColumn('employees', 'bank_name')) {
                $table->string('bank_name')->nullable()->after('bank_account');
            }
            if (!Schema::hasColumn('employees', 'bank_branch')) {
                $table->string('bank_branch')->nullable()->after('bank_name');
            }
            
            // Address
            if (!Schema::hasColumn('employees', 'address')) {
                $table->string('address', 500)->nullable()->after('bank_branch');
            }
            if (!Schema::hasColumn('employees', 'city')) {
                $table->string('city', 100)->nullable()->after('address');
            }
            if (!Schema::hasColumn('employees', 'region')) {
                $table->string('region', 100)->nullable()->after('city');
            }
            if (!Schema::hasColumn('employees', 'postal_code')) {
                $table->string('postal_code', 20)->nullable()->after('region');
            }
            if (!Schema::hasColumn('employees', 'country')) {
                $table->string('country', 100)->nullable()->after('postal_code');
            }
            
            // Emergency contact
            if (!Schema::hasColumn('employees', 'emergency_contact_name')) {
                $table->string('emergency_contact_name')->nullable()->after('country');
            }
            if (!Schema::hasColumn('employees', 'emergency_contact_phone')) {
                $table->string('emergency_contact_phone', 20)->nullable()->after('emergency_contact_name');
            }
            if (!Schema::hasColumn('employees', 'emergency_contact_relationship')) {
                $table->string('emergency_contact_relationship', 100)->nullable()->after('emergency_contact_phone');
            }
            
            // Work
            if (!Schema::hasColumn('employees', 'work_schedule')) {
                $table->string('work_schedule')->nullable()->after('emergency_contact_relationship');
            }
            if (!Schema::hasColumn('employees', 'education_level')) {
                $table->string('education_level')->nullable()->after('work_schedule');
            }
            
            // JSON fields
            if (!Schema::hasColumn('employees', 'professional_qualifications')) {
                $table->json('professional_qualifications')->nullable()->after('education_level');
            }
            if (!Schema::hasColumn('employees', 'certifications')) {
                $table->json('certifications')->nullable()->after('professional_qualifications');
            }
            if (!Schema::hasColumn('employees', 'skills')) {
                $table->json('skills')->nullable()->after('certifications');
            }
            if (!Schema::hasColumn('employees', 'languages')) {
                $table->json('languages')->nullable()->after('skills');
            }
            if (!Schema::hasColumn('employees', 'tax_information')) {
                $table->json('tax_information')->nullable()->after('benefits');
            }
            if (!Schema::hasColumn('employees', 'social_security_info')) {
                $table->json('social_security_info')->nullable()->after('tax_information');
            }
            if (!Schema::hasColumn('employees', 'health_insurance_info')) {
                $table->json('health_insurance_info')->nullable()->after('social_security_info');
            }
            if (!Schema::hasColumn('employees', 'pension_info')) {
                $table->json('pension_info')->nullable()->after('health_insurance_info');
            }
            
            // Photo and documents
            if (!Schema::hasColumn('employees', 'profile_photo')) {
                $table->string('profile_photo')->nullable()->after('languages');
            }
            if (!Schema::hasColumn('employees', 'documents')) {
                $table->json('documents')->nullable()->after('profile_photo');
            }
            
            // Other fields
            if (!Schema::hasColumn('employees', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            }
            if (!Schema::hasColumn('employees', 'contracts_count')) {
                $table->unsignedInteger('contracts_count')->default(0)->after('updated_by');
            }
            if (!Schema::hasColumn('employees', 'last_performance_review')) {
                $table->date('last_performance_review')->nullable()->after('contracts_count');
            }
            if (!Schema::hasColumn('employees', 'next_performance_review')) {
                $table->date('next_performance_review')->nullable()->after('last_performance_review');
            }
            if (!Schema::hasColumn('employees', 'leave_balance')) {
                $table->decimal('leave_balance', 8, 2)->default(0)->after('next_performance_review');
            }
            if (!Schema::hasColumn('employees', 'overtime_hours')) {
                $table->decimal('overtime_hours', 8, 2)->default(0)->after('leave_balance');
            }
            if (!Schema::hasColumn('employees', 'notes')) {
                $table->text('notes')->nullable()->after('overtime_hours');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Drop all columns we added
            $columns = [
                'phone', 'date_of_birth', 'gender', 'national_id', 'passport_number', 'tin_number',
                'nssf_number', 'nhif_number', 'manager_id', 'bank_account', 'bank_name', 'bank_branch',
                'address', 'city', 'region', 'postal_code', 'country', 'emergency_contact_name',
                'emergency_contact_phone', 'emergency_contact_relationship', 'work_schedule',
                'education_level', 'professional_qualifications', 'certifications', 'skills', 'languages',
                'tax_information', 'social_security_info', 'health_insurance_info', 'pension_info',
                'profile_photo', 'documents', 'updated_by', 'contracts_count', 'last_performance_review',
                'next_performance_review', 'leave_balance', 'overtime_hours', 'notes'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('employees', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
