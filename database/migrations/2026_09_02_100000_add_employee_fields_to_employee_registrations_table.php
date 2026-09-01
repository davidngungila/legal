<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_registrations', function (Blueprint $table) {
            // Legal / compliance IDs (from employee form)
            $table->string('national_id')->nullable()->after('phone_number');
            $table->string('passport_number')->nullable()->after('national_id');
            $table->string('tin_number')->nullable()->after('passport_number');
            $table->string('nssf_number')->nullable()->after('tin_number');
            $table->string('nhif_number')->nullable()->after('nssf_number');

            // Employment details (employee-level)
            $table->string('department')->nullable()->after('nhif_number');
            $table->string('position')->nullable()->after('department');
            $table->string('employment_type')->nullable()->after('position');
            $table->string('employee_status')->nullable()->after('employment_type');
            $table->string('role')->nullable()->after('employee_status');
            $table->unsignedBigInteger('manager_id')->nullable()->after('role');
            $table->string('work_schedule')->nullable()->after('manager_id');
            $table->string('education_level')->nullable()->after('work_schedule');

            // Skills & qualifications
            $table->text('skills')->nullable()->after('education_level');
            $table->text('languages')->nullable()->after('skills');
            $table->text('professional_qualifications')->nullable()->after('languages');
            $table->text('certifications')->nullable()->after('professional_qualifications');

            // Compensation & bank
            $table->decimal('salary', 15, 2)->nullable()->after('certifications');
            $table->string('currency')->nullable()->after('salary');
            $table->string('payment_frequency')->nullable()->after('currency');
            $table->string('bank_name')->nullable()->after('payment_frequency');
            $table->string('bank_branch')->nullable()->after('bank_name');
            $table->string('bank_account')->nullable()->after('bank_branch');

            // Address & emergency contact
            $table->string('address')->nullable()->after('bank_account');
            $table->string('city')->nullable()->after('address');
            $table->string('region')->nullable()->after('city');
            $table->string('postal_code')->nullable()->after('region');
            $table->string('country')->nullable()->after('postal_code');
            $table->string('emergency_contact_name')->nullable()->after('country');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            $table->string('emergency_contact_relationship')->nullable()->after('emergency_contact_phone');

            // Login credentials (used to create the user on approval)
            $table->string('login_email')->nullable()->after('emergency_contact_relationship');
            $table->string('password')->nullable()->after('login_email');

            $table->foreign('manager_id')->references('id')->on('employees')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('employee_registrations', function (Blueprint $table) {
            $table->dropForeign(['manager_id']);

            $columns = [
                'national_id', 'passport_number', 'tin_number', 'nssf_number', 'nhif_number',
                'department', 'position', 'employment_type', 'employee_status', 'role',
                'manager_id', 'work_schedule', 'education_level',
                'skills', 'languages', 'professional_qualifications', 'certifications',
                'salary', 'currency', 'payment_frequency', 'bank_name', 'bank_branch', 'bank_account',
                'address', 'city', 'region', 'postal_code', 'country',
                'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relationship',
                'login_email', 'password',
            ];

            $table->dropColumn($columns);
        });
    }
};