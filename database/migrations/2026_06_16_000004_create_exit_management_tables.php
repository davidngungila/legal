<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('exit_cases')) {
            Schema::create('exit_cases', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id');
                $table->unsignedBigInteger('employee_id');
                $table->string('exit_number')->unique();
                $table->enum('exit_type', [
                    'resignation',
                    'misconduct_termination',
                    'retrenchment',
                    'mutual_separation',
                    'retirement',
                    'death_in_service'
                ]);
                $table->date('exit_date')->nullable();
                $table->date('notice_date')->nullable();
                $table->text('reason')->nullable();
                $table->string('status')->default('initiated');
                $table->unsignedBigInteger('initiated_by');
                $table->timestamps();
                
                $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
                $table->foreign('initiated_by')->references('id')->on('users')->onDelete('cascade');
                $table->index(['client_id', 'status']);
            });
        }

        if (!Schema::hasTable('exit_checklists')) {
            Schema::create('exit_checklists', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('exit_case_id');
                $table->string('item_name');
                $table->string('category');
                $table->boolean('completed')->default(false);
                $table->unsignedBigInteger('completed_by')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                
                $table->foreign('exit_case_id')->references('id')->on('exit_cases')->onDelete('cascade');
                $table->foreign('completed_by')->references('id')->on('users')->onDelete('set null');
            });
        }

        if (!Schema::hasTable('retrenchment_cases')) {
            Schema::create('retrenchment_cases', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id');
                $table->text('business_justification')->nullable();
                $table->date('consultation_notice_date')->nullable();
                $table->enum('selection_criteria', [
                    'lifo',
                    'fifo',
                    'attendance',
                    'performance',
                    'disciplinary',
                    'combined'
                ])->nullable();
                $table->string('status')->default('initiated');
                $table->unsignedBigInteger('initiated_by');
                $table->timestamps();
                
                $table->foreign('initiated_by')->references('id')->on('users')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('retrenchment_employees')) {
            Schema::create('retrenchment_employees', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('retrenchment_case_id');
                $table->unsignedBigInteger('employee_id');
                $table->decimal('selection_score', 10, 2)->nullable();
                $table->boolean('selected')->default(false);
                $table->decimal('redundancy_pay', 15, 2)->nullable();
                $table->decimal('final_settlement', 15, 2)->nullable();
                $table->timestamps();
                
                $table->foreign('retrenchment_case_id')->references('id')->on('retrenchment_cases')->onDelete('cascade');
                $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('exit_settlements')) {
            Schema::create('exit_settlements', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('exit_case_id');
                $table->decimal('final_salary', 15, 2)->nullable();
                $table->decimal('leave_pay', 15, 2)->nullable();
                $table->decimal('notice_pay', 15, 2)->nullable();
                $table->decimal('bonus_pay', 15, 2)->nullable();
                $table->decimal('other_payments', 15, 2)->nullable();
                $table->decimal('total_settlement', 15, 2)->nullable();
                $table->string('status')->default('pending');
                $table->timestamps();
                
                $table->foreign('exit_case_id')->references('id')->on('exit_cases')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('exit_settlements');
        Schema::dropIfExists('retrenchment_employees');
        Schema::dropIfExists('retrenchment_cases');
        Schema::dropIfExists('exit_checklists');
        Schema::dropIfExists('exit_cases');
    }
};
