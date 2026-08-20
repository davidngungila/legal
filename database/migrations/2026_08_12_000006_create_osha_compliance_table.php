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
        if (!Schema::hasTable('osha_compliance_records')) {
            Schema::create('osha_compliance_records', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id');
                $table->unsignedBigInteger('employee_id')->nullable();
                $table->string('record_type'); // accident, incident, inspection, training, certification
                $table->date('incident_date');
                $table->string('severity')->nullable(); // minor, moderate, severe, critical
                $table->text('description')->nullable();
                $table->text('root_cause')->nullable();
                $table->text('corrective_action')->nullable();
                $table->date('corrective_action_date')->nullable();
                $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
                $table->unsignedBigInteger('reported_by')->nullable();
                $table->timestamp('reported_at')->nullable();
                $table->unsignedBigInteger('investigated_by')->nullable();
                $table->timestamp('investigated_at')->nullable();
                $table->text('investigation_notes')->nullable();
                $table->boolean('requires_follow_up')->default(false);
                $table->date('follow_up_date')->nullable();
                $table->text('follow_up_notes')->nullable();
                $table->timestamps();

                $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
                $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null');
                $table->foreign('reported_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('investigated_by')->references('id')->on('users')->onDelete('set null');
                
                $table->index(['client_id', 'record_type']);
                $table->index(['client_id', 'status']);
                $table->index(['client_id', 'incident_date']);
                $table->index(['employee_id', 'incident_date']);
            });
        }

        if (!Schema::hasTable('osha_safety_trainings')) {
            Schema::create('osha_safety_trainings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id');
                $table->string('training_name');
                $table->text('description')->nullable();
                $table->enum('training_type', ['mandatory', 'recommended', 'optional'])->default('recommended');
                $table->integer('duration_hours')->default(0);
                $table->integer('validity_months')->nullable(); // How long certification is valid
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
                
                $table->index(['client_id', 'training_type']);
                $table->index(['client_id', 'is_active']);
            });
        }

        if (!Schema::hasTable('osha_employee_certifications')) {
            Schema::create('osha_employee_certifications', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id');
                $table->unsignedBigInteger('employee_id');
                $table->unsignedBigInteger('training_id');
                $table->date('completion_date');
                $table->date('expiry_date')->nullable();
                $table->string('certificate_number')->nullable();
                $table->string('issuing_authority')->nullable();
                $table->enum('status', ['active', 'expired', 'suspended'])->default('active');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
                $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
                $table->foreign('training_id')->references('id')->on('osha_safety_trainings')->onDelete(' cascade');
                
                $table->index(['client_id', 'employee_id']);
                $table->index(['client_id', 'status']);
                $table->index(['client_id', 'expiry_date']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('osha_employee_certifications');
        Schema::dropIfExists('osha_safety_trainings');
        Schema::dropIfExists('osha_compliance_records');
    }
};
