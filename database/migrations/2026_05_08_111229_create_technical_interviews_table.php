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
        Schema::create('technical_interviews', function (Blueprint $table) {
            $table->id();
            $table->string('interview_number')->unique();
            $table->unsignedBigInteger('hr_interview_id');
            $table->string('candidate_name');
            $table->string('job_title');
            $table->date('interview_date');
            $table->string('interviewer_name');
            $table->unsignedBigInteger('interviewer_id');
            $table->unsignedBigInteger('department_manager_id')->nullable();
            
            // Technical assessment areas
            $table->text('business_process_knowledge')->nullable();
            $table->text('technical_skills_assessment')->nullable();
            $table->text('physical_capabilities')->nullable();
            $table->text('practical_test_results')->nullable();
            $table->text('other_technical_areas')->nullable();
            
            // Assessment results
            $table->enum('technical_result', ['pass', 'fail', 'na'])->default('na');
            $table->text('technical_comments')->nullable();
            $table->enum('manager_approval', ['approved', 'rejected', 'pending'])->default('pending');
            $table->text('manager_comments')->nullable();
            
            // Attachments
            $table->text('assessment_report_path')->nullable();
            $table->text('signed_file_path')->nullable();
            
            // Workflow
            $table->enum('status', ['draft', 'submitted', 'interviewer_completed', 'manager_approved', 'rejected'])->default('draft');
            $table->timestamp('interviewer_completed_at')->nullable();
            $table->timestamp('manager_approved_at')->nullable();
            $table->timestamps();
            
            $table->foreign('hr_interview_id')->references('id')->on('hr_competency_interviews');
            $table->foreign('interviewer_id')->references('id')->on('users');
            $table->foreign('department_manager_id')->references('id')->on('users');
            $table->index(['interview_number', 'candidate_name']);
            $table->index('status');
            $table->index('interview_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technical_interviews');
    }
};
