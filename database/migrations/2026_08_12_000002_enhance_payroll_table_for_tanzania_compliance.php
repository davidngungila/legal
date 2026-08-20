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
        Schema::table('payrolls', function (Blueprint $table) {
            // Tanzania-specific statutory fields
            if (!Schema::hasColumn('payrolls', 'nssf_employee')) {
                $table->decimal('nssf_employee', 10, 2)->default(0)->after('social_security');
            }
            if (!Schema::hasColumn('payrolls', 'nssf_employer')) {
                $table->decimal('nssf_employer', 10, 2)->default(0)->after('nssf_employee');
            }
            if (!Schema::hasColumn('payrolls', 'wcf')) {
                $table->decimal('wcf', 10, 2)->default(0)->after('nssf_employer');
            }
            if (!Schema::hasColumn('payrolls', 'sdl')) {
                $table->decimal('sdl', 10, 2)->default(0)->after('wcf');
            }
            if (!Schema::hasColumn('payrolls', 'heslb')) {
                $table->decimal('heslb', 10, 2)->default(0)->after('sdl');
            }
            if (!Schema::hasColumn('payrolls', 'trade_union')) {
                $table->decimal('trade_union', 10, 2)->default(0)->after('heslb');
            }
            
            // Enhanced calculation fields
            if (!Schema::hasColumn('payrolls', 'taxable_income')) {
                $table->decimal('taxable_income', 10, 2)->default(0)->after('gross_pay');
            }
            if (!Schema::hasColumn('payrolls', 'hourly_rate')) {
                $table->decimal('hourly_rate', 8, 2)->default(0)->after('basic_salary');
            }
            if (!Schema::hasColumn('payrolls', 'daily_rate')) {
                $table->decimal('daily_rate', 8, 2)->default(0)->after('hourly_rate');
            }
            if (!Schema::hasColumn('payrolls', 'rest_day_hours')) {
                $table->decimal('rest_day_hours', 5, 2)->default(0)->after('overtime_hours');
            }
            if (!Schema::hasColumn('payrolls', 'rest_day_pay')) {
                $table->decimal('rest_day_pay', 10, 2)->default(0)->after('rest_day_hours');
            }
            if (!Schema::hasColumn('payrolls', 'ph_hours')) {
                $table->decimal('ph_hours', 5, 2)->default(0)->after('rest_day_pay');
            }
            if (!Schema::hasColumn('payrolls', 'ph_pay')) {
                $table->decimal('ph_pay', 10, 2)->default(0)->after('ph_hours');
            }
            if (!Schema::hasColumn('payrolls', 'night_hours')) {
                $table->decimal('night_hours', 5, 2)->default(0)->after('ph_pay');
            }
            if (!Schema::hasColumn('payrolls', 'night_allowance')) {
                $table->decimal('night_allowance', 10, 2)->default(0)->after('night_hours');
            }
            if (!Schema::hasColumn('payrolls', 'unpaid_leave_days')) {
                $table->decimal('unpaid_leave_days', 5, 2)->default(0)->after('night_allowance');
            }
            if (!Schema::hasColumn('payrolls', 'unpaid_leave_deduction')) {
                $table->decimal('unpaid_leave_deduction', 10, 2)->default(0)->after('unpaid_leave_days');
            }
            
            // Workflow and audit fields
            if (!Schema::hasColumn('payrolls', 'workflow_state')) {
                $table->enum('workflow_state', ['prepared', 'reviewed', 'approved', 'locked', 'reversed'])->default('prepared')->after('status');
            }
            if (!Schema::hasColumn('payrolls', 'initiated_by')) {
                $table->unsignedBigInteger('initiated_by')->nullable()->after('workflow_state');
            }
            if (!Schema::hasColumn('payrolls', 'initiated_at')) {
                $table->timestamp('initiated_at')->nullable()->after('initiated_by');
            }
            if (!Schema::hasColumn('payrolls', 'reviewed_by')) {
                $table->unsignedBigInteger('reviewed_by')->nullable()->after('initiated_at');
            }
            if (!Schema::hasColumn('payrolls', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            }
            if (!Schema::hasColumn('payrolls', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('reviewed_at');
            }
            if (!Schema::hasColumn('payrolls', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('payrolls', 'locked_at')) {
                $table->timestamp('locked_at')->nullable()->after('approved_at');
            }
            if (!Schema::hasColumn('payrolls', 'locked_by')) {
                $table->unsignedBigInteger('locked_by')->nullable()->after('locked_at');
            }
            if (!Schema::hasColumn('payrolls', 'performance_complete')) {
                $table->boolean('performance_complete')->default(true)->after('locked_by');
            }
            if (!Schema::hasColumn('payrolls', 'salary_hold')) {
                $table->boolean('salary_hold')->default(false)->after('performance_complete');
            }
            if (!Schema::hasColumn('payrolls', 'salary_hold_reason')) {
                $table->string('salary_hold_reason')->nullable()->after('salary_hold');
            }
            
            // Foreign keys
            if (!Schema::hasColumn('payrolls', 'initiated_by')) {
                $table->foreign('initiated_by')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('payrolls', 'reviewed_by')) {
                $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('payrolls', 'approved_by')) {
                $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('payrolls', 'locked_by')) {
                $table->foreign('locked_by')->references('id')->on('users')->onDelete('set null');
            }
            
            // Indexes
            $table->index(['client_id', 'workflow_state']);
            $table->index(['client_id', 'payroll_period', 'workflow_state']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $columns = [
                'nssf_employee', 'nssf_employer', 'wcf', 'sdl', 'heslb', 'trade_union',
                'taxable_income', 'hourly_rate', 'daily_rate', 'rest_day_hours', 'rest_day_pay',
                'ph_hours', 'ph_pay', 'night_hours', 'night_allowance', 'unpaid_leave_days',
                'unpaid_leave_deduction', 'workflow_state', 'initiated_by', 'initiated_at',
                'reviewed_by', 'reviewed_at', 'approved_by', 'approved_at', 'locked_at',
                'locked_by', 'performance_complete', 'salary_hold', 'salary_hold_reason'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('payrolls', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
