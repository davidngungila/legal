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
        Schema::create('employee_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('employee_number')->unique();
            $table->unsignedBigInteger('hr_interview_id');
            $table->unsignedBigInteger('technical_interview_id')->nullable();
            
            // Personal details (fetched from interview)
            $table->string('surname');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('birthplace');
            $table->date('date_of_birth');
            $table->integer('age');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('residence_area');
            $table->string('permanent_residence');
            $table->string('postal_address')->nullable();
            $table->string('email_address');
            $table->string('phone_number');
            $table->string('place_of_recruitment');
            $table->string('work_station');
            
            // Employment details
            $table->string('type_of_contract');
            $table->text('job_descriptions');
            $table->date('date_employed');
            $table->text('terms_conditions');
            
            // Consent and signatures
            $table->boolean('information_consent')->default(false);
            $table->text('employee_signature')->nullable();
            $table->date('signature_date')->nullable();
            $table->text('signed_document_path')->nullable();
            
            // Ranking and history
            $table->text('ranking_details')->nullable();
            $table->text('employment_history')->nullable();
            
            // Workflow
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            $table->foreign('hr_interview_id')->references('id')->on('hr_competency_interviews');
            $table->foreign('technical_interview_id')->references('id')->on('technical_interviews');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users');
            $table->index(['employee_number', 'surname', 'first_name']);
            $table->index('status');
            $table->index('date_employed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_registrations');
    }
};
