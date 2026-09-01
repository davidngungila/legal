<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Exit Cases: link to DisciplinaryCase and EmploymentContract
        Schema::table('exit_cases', function (Blueprint $table) {
            $table->foreignId('disciplinary_case_id')->nullable()->after('employee_id')->constrained('disciplinary_cases')->nullOnDelete();
            $table->foreignId('employment_contract_id')->nullable()->after('disciplinary_case_id')->constrained('employment_contracts')->nullOnDelete();
        });

        // Legal Cases: link to DisciplinaryCase
        Schema::table('legal_cases', function (Blueprint $table) {
            $table->foreignId('disciplinary_case_id')->nullable()->after('employee_id')->constrained('disciplinary_cases')->nullOnDelete();
        });

        // Employee Registrations: link to Employee
        Schema::table('employee_registrations', function (Blueprint $table) {
            $table->foreignId('employee_id')->nullable()->after('client_id')->constrained('employees')->nullOnDelete();
        });

        // Add missing FK constraints on legal_cases (columns exist but no FK)
        Schema::table('legal_cases', function (Blueprint $table) {
            $table->foreign('employee_id')->references('id')->on('employees')->nullOnDelete();
            $table->foreign('client_id')->references('id')->on('clients')->nullOnDelete();
            $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('exit_cases', function (Blueprint $table) {
            $table->dropForeign(['disciplinary_case_id']);
            $table->dropForeign(['employment_contract_id']);
            $table->dropColumn(['disciplinary_case_id', 'employment_contract_id']);
        });

        Schema::table('legal_cases', function (Blueprint $table) {
            $table->dropForeign(['disciplinary_case_id']);
            $table->dropColumn('disciplinary_case_id');
            $table->dropForeign(['employee_id']);
            $table->dropForeign(['client_id']);
            $table->dropForeign(['assigned_to']);
            $table->dropForeign(['created_by']);
        });

        Schema::table('employee_registrations', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->dropColumn('employee_id');
        });
    }
};
