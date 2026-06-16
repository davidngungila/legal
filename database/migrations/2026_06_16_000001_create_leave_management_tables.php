<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('leave_types')) {
            Schema::create('leave_types', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->string('type_name');
                $table->decimal('entitlement_days')->default(0);
                $table->decimal('accrual_rate')->default(0);
                $table->integer('eligibility_months')->default(0);
                $table->integer('cycle_months')->default(12);
                $table->boolean('is_paid')->default(true);
                $table->decimal('pay_rate')->default(100); // %
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->index(['client_id', 'is_active']);
            });
        }

        if (!Schema::hasTable('leave_entitlements')) {
            Schema::create('leave_entitlements', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id');
                $table->unsignedBigInteger('employee_id');
                $table->unsignedBigInteger('leave_type_id');
                $table->decimal('entitlement_days')->default(0);
                $table->decimal('taken_days')->default(0);
                $table->decimal('balance_days')->default(0);
                $table->date('cycle_start')->nullable();
                $table->date('cycle_end')->nullable();
                $table->timestamps();
                $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
                $table->foreign('leave_type_id')->references('id')->on('leave_types')->onDelete('cascade');
                $table->index(['client_id', 'employee_id']);
                $table->index(['client_id', 'leave_type_id']);
            });
        }

        if (!Schema::hasTable('leave_approvals')) {
            Schema::create('leave_approvals', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id');
                $table->unsignedBigInteger('application_id');
                $table->unsignedBigInteger('approver_id');
                $table->string('action'); // approve/reject/partial
                $table->text('comments')->nullable();
                $table->timestamp('actioned_at')->nullable();
                $table->timestamps();
                $table->foreign('application_id')->references('id')->on('leave_requests')->onDelete('cascade');
                $table->foreign('approver_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('leave_balances')) {
            Schema::create('leave_balances', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id');
                $table->unsignedBigInteger('employee_id');
                $table->unsignedBigInteger('leave_type_id');
                $table->integer('month');
                $table->integer('year');
                $table->decimal('opening_balance')->default(0);
                $table->decimal('accrued')->default(0);
                $table->decimal('taken')->default(0);
                $table->decimal('closing_balance')->default(0);
                $table->timestamps();
                $table->index(['client_id', 'employee_id', 'leave_type_id', 'year', 'month'], 'lb_client_emp_type_year_month_idx');
            });
        }

        if (Schema::hasTable('leave_requests') && !Schema::hasColumns('leave_requests', ['client_id'])) {
            Schema::table('leave_requests', function (Blueprint $table) {
                $table->unsignedBigInteger('client_id')->nullable()->after('id');
            });
        }

        if (Schema::hasTable('leave_requests') && !Schema::hasColumns('leave_requests', ['leave_type_id'])) {
            Schema::table('leave_requests', function (Blueprint $table) {
                $table->unsignedBigInteger('leave_type_id')->nullable()->after('client_id');
            });
        }

        if (Schema::hasTable('leave_requests') && !Schema::hasColumns('leave_requests', ['days_approved'])) {
            Schema::table('leave_requests', function (Blueprint $table) {
                $table->decimal('days_approved')->default(0)->after('days');
            });
        }

        if (Schema::hasTable('leave_requests') && !Schema::hasColumns('leave_requests', ['applied_at'])) {
            Schema::table('leave_requests', function (Blueprint $table) {
                $table->timestamp('applied_at')->nullable()->after('reason');
            });
        }

        if (Schema::hasTable('leave_requests') && !Schema::hasColumns('leave_requests', ['workflow_status'])) {
            Schema::table('leave_requests', function (Blueprint $table) {
                $table->string('workflow_status')->default('pending')->after('status');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_balances');
        Schema::dropIfExists('leave_approvals');
        Schema::dropIfExists('leave_entitlements');
        Schema::dropIfExists('leave_types');
    }
};
