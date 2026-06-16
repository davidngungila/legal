<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('disciplinary_cases')) {
            Schema::create('disciplinary_cases', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id');
                $table->unsignedBigInteger('employee_id');
                $table->string('case_number')->unique();
                $table->enum('case_type', ['minor', 'major']);
                $table->date('incident_date');
                $table->text('incident_description');
                $table->unsignedBigInteger('reported_by');
                $table->string('status')->default('reported');
                $table->timestamps();
                
                $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
                $table->foreign('reported_by')->references('id')->on('users')->onDelete('cascade');
                $table->index(['client_id', 'status']);
            });
        }

        if (!Schema::hasTable('disciplinary_warnings')) {
            Schema::create('disciplinary_warnings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id');
                $table->unsignedBigInteger('employee_id');
                $table->unsignedBigInteger('case_id')->nullable();
                $table->enum('warning_type', ['verbal', 'first', 'second', 'final']);
                $table->date('issued_date');
                $table->date('expiry_date');
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('issued_by');
                $table->timestamps();
                
                $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
                $table->foreign('case_id')->references('id')->on('disciplinary_cases')->onDelete('set null');
                $table->foreign('issued_by')->references('id')->on('users')->onDelete('cascade');
                $table->index(['client_id', 'employee_id', 'is_active']);
            });
        }

        if (!Schema::hasTable('show_cause_notices')) {
            Schema::create('show_cause_notices', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('case_id');
                $table->date('sent_date');
                $table->date('response_deadline');
                $table->timestamp('response_received_at')->nullable();
                $table->text('response_text')->nullable();
                $table->string('status')->default('pending');
                $table->timestamps();
                
                $table->foreign('case_id')->references('id')->on('disciplinary_cases')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('disciplinary_hearings')) {
            Schema::create('disciplinary_hearings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('case_id');
                $table->date('hearing_date');
                $table->time('hearing_time');
                $table->string('venue');
                $table->timestamp('notice_sent_at')->nullable();
                $table->text('committee_members')->nullable();
                $table->string('employee_representative')->nullable();
                $table->text('proceedings_notes')->nullable();
                $table->timestamps();
                
                $table->foreign('case_id')->references('id')->on('disciplinary_cases')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('disciplinary_outcomes')) {
            Schema::create('disciplinary_outcomes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('case_id');
                $table->enum('outcome_type', [
                    'verbal_warning',
                    'first_warning',
                    'second_warning',
                    'final_warning',
                    'summary_dismissal',
                    'dismissal_with_notice',
                    'demotion',
                    'suspension_without_pay',
                    'transfer',
                    'case_dismissed'
                ]);
                $table->date('outcome_date');
                $table->unsignedBigInteger('issued_by');
                $table->text('rationale')->nullable();
                $table->date('appeal_deadline')->nullable();
                $table->timestamps();
                
                $table->foreign('case_id')->references('id')->on('disciplinary_cases')->onDelete('cascade');
                $table->foreign('issued_by')->references('id')->on('users')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('disciplinary_appeals')) {
            Schema::create('disciplinary_appeals', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('case_id');
                $table->timestamp('appeal_filed_at')->nullable();
                $table->unsignedBigInteger('appeal_by')->nullable();
                $table->text('appeal_grounds')->nullable();
                $table->string('appeal_decision')->nullable();
                $table->date('decision_date')->nullable();
                $table->unsignedBigInteger('appeal_authority_id')->nullable();
                $table->timestamps();
                
                $table->foreign('case_id')->references('id')->on('disciplinary_cases')->onDelete('cascade');
                $table->foreign('appeal_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('appeal_authority_id')->references('id')->on('users')->onDelete('set null');
            });
        }

        if (!Schema::hasTable('disciplinary_documents')) {
            Schema::create('disciplinary_documents', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('case_id');
                $table->string('doc_type');
                $table->timestamp('generated_at')->nullable();
                $table->string('file_path')->nullable();
                $table->timestamp('served_at')->nullable();
                $table->unsignedBigInteger('served_by')->nullable();
                $table->timestamps();
                
                $table->foreign('case_id')->references('id')->on('disciplinary_cases')->onDelete('cascade');
                $table->foreign('served_by')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('disciplinary_documents');
        Schema::dropIfExists('disciplinary_appeals');
        Schema::dropIfExists('disciplinary_outcomes');
        Schema::dropIfExists('disciplinary_hearings');
        Schema::dropIfExists('show_cause_notices');
        Schema::dropIfExists('disciplinary_warnings');
        Schema::dropIfExists('disciplinary_cases');
    }
};
