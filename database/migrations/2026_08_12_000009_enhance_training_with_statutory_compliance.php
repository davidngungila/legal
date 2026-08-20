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
        // Add statutory compliance fields to training tables
        Schema::table('training_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('training_sessions', 'is_statutory')) {
                $table->boolean('is_statutory')->default(false)->after('instructor');
            }
            if (!Schema::hasColumn('training_sessions', 'statutory_authority')) {
                $table->string('statutory_authority')->nullable()->after('is_statutory'); // OSHA, etc.
            }
            if (!Schema::hasColumn('training_sessions', 'validity_period_months')) {
                $table->integer('validity_period_months')->nullable()->after('statutory_authority');
            }
            if (!Schema::hasColumn('training_sessions', 'compliance_deadline')) {
                $table->date('compliance_deadline')->nullable()->after('validity_period_months');
            }
            if (!Schema::hasColumn('training_sessions', 'certification_required')) {
                $table->boolean('certification_required')->default(false)->after('compliance_deadline');
            }
        });

        Schema::table('training_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('training_plans', 'compliance_status')) {
                $table->enum('compliance_status', ['compliant', 'non_compliant', 'pending'])->nullable()->after('status');
            }
            if (!Schema::hasColumn('training_plans', 'last_compliance_check')) {
                $table->date('last_compliance_check')->nullable()->after('compliance_status');
            }
            if (!Schema::hasColumn('training_plans', 'next_compliance_check')) {
                $table->date('next_compliance_check')->nullable()->after('last_compliance_check');
            }
        });

        // Create employee training compliance tracking table
        if (!Schema::hasTable('employee_training_compliance')) {
            Schema::create('employee_training_compliance', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id');
                $table->unsignedBigInteger('employee_id');
                $table->unsignedBigInteger('training_session_id')->nullable();
                $table->unsignedBigInteger('training_plan_id')->nullable();
                $table->string('compliance_type'); // statutory, mandatory, optional
                $table->string('statutory_requirement')->nullable(); // OSHA safety, etc.
                $table->date('completion_date')->nullable();
                $table->date('expiry_date')->nullable();
                $table->enum('status', ['not_started', 'in_progress', 'completed', 'expired', 'overdue'])->default('not_started');
                $table->decimal('completion_percentage', 5, 2)->default(0);
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('certified_by')->nullable();
                $table->timestamp('certified_at')->nullable();
                $table->timestamps();

                $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
                $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
                $table->foreign('training_session_id')->references('id')->on('training_sessions')->onDelete('set null');
                $table->foreign('training_plan_id')->references('id')->on('training_plans')->onDelete('set null');
                $table->foreign('certified_by')->references('id')->on('users')->onDelete('set null');
                
                $table->index(['client_id', 'employee_id']);
                $table->index(['client_id', 'status']);
                $table->index(['client_id', 'expiry_date']);
                $table->index(['employee_id', 'statutory_requirement'], 'emp_statutory_idx');
            });
        }

        // Note: Default statutory training requirements will be created per employee via seeder or employee creation logic
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_training_compliance');
        
        Schema::table('training_sessions', function (Blueprint $table) {
            $columns = ['is_statutory', 'statutory_authority', 'validity_period_months', 'compliance_deadline', 'certification_required'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('training_sessions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('training_plans', function (Blueprint $table) {
            $columns = ['compliance_status', 'last_compliance_check', 'next_compliance_check'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('training_plans', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
