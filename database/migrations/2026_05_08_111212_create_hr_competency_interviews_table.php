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
        Schema::create('hr_competency_interviews', function (Blueprint $table) {
            $table->id();
            $table->string('interview_number')->unique();
            $table->string('job_title');
            $table->date('interview_date');
            $table->string('candidate_name');
            $table->string('interviewer_name');
            $table->enum('military_service_status', ['completed', 'didnt_attend', 'na']);
            $table->string('military_certificate_path')->nullable();
            $table->string('place_of_recruitment');
            $table->integer('total_years_experience');
            
            // Rating fields (0-5 scale)
            $table->tinyInteger('education_job_knowledge')->default(0);
            $table->text('education_job_knowledge_comment')->nullable();
            $table->tinyInteger('relevant_job_experience')->default(0);
            $table->tinyInteger('major_previous_achievement')->default(0);
            $table->tinyInteger('language_fluency')->default(0);
            $table->text('language_fluency_comment')->nullable();
            
            // Core competencies
            $table->tinyInteger('interactive_communication')->default(0);
            $table->text('interactive_communication_comment')->nullable();
            $table->tinyInteger('accountability')->default(0);
            $table->text('accountability_comment')->nullable();
            $table->tinyInteger('work_excellence')->default(0);
            $table->text('work_excellence_comment')->nullable();
            
            // Functional competencies
            $table->tinyInteger('functional_competencies')->default(0);
            $table->text('functional_competencies_comment')->nullable();
            $table->tinyInteger('planning_organizing')->default(0);
            $table->text('planning_organizing_comment')->nullable();
            $table->tinyInteger('problem_solving')->default(0);
            $table->text('problem_solving_comment')->nullable();
            $table->tinyInteger('attention_to_details')->default(0);
            $table->text('attention_to_details_comment')->nullable();
            $table->tinyInteger('multitasking')->default(0);
            $table->text('multitasking_comment')->nullable();
            $table->tinyInteger('continuous_improvement')->default(0);
            $table->text('continuous_improvement_comment')->nullable();
            $table->tinyInteger('compliance')->default(0);
            $table->text('compliance_comment')->nullable();
            $table->tinyInteger('creative_innovation')->default(0);
            $table->text('creative_innovation_comment')->nullable();
            $table->tinyInteger('negotiation')->default(0);
            $table->text('negotiation_comment')->nullable();
            $table->tinyInteger('teamwork')->default(0);
            $table->text('teamwork_comment')->nullable();
            $table->tinyInteger('adaptability_flexibility')->default(0);
            $table->text('adaptability_flexibility_comment')->nullable();
            
            // Managerial competencies
            $table->tinyInteger('leadership')->default(0);
            $table->tinyInteger('managing_developing_people')->default(0);
            $table->tinyInteger('managing_change')->default(0);
            $table->tinyInteger('making_decisions')->default(0);
            
            // Overall assessment
            $table->tinyInteger('overall_rating')->default(0);
            $table->text('main_strength')->nullable();
            $table->text('main_weakness')->nullable();
            
            // Additional details
            $table->enum('relative_inside_client', ['yes', 'no']);
            $table->string('relative_name')->nullable();
            $table->string('birthplace');
            $table->string('residence');
            $table->enum('employed_before', ['yes', 'no']);
            $table->enum('reference_checking', ['yes', 'no']);
            $table->decimal('current_salary', 10, 2)->nullable();
            $table->integer('required_notice_days')->nullable();
            $table->enum('current_employer_entity', ['government', 'private']);
            
            // Recommendation
            $table->enum('recruiter_recommendation', ['accepted', 'not_accepted', 'waiting_list']);
            $table->string('recommended_job_title')->nullable();
            
            // Workflow and signatures
            $table->unsignedBigInteger('interviewer_id');
            $table->text('interviewer_signature')->nullable();
            $table->unsignedBigInteger('hr_manager_id')->nullable();
            $table->timestamp('hr_manager_approved_at')->nullable();
            $table->text('signed_file_path')->nullable();
            $table->enum('status', ['draft', 'submitted', 'hr_approved', 'rejected'])->default('draft');
            $table->timestamps();
            
            $table->foreign('interviewer_id')->references('id')->on('users');
            $table->foreign('hr_manager_id')->references('id')->on('users');
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
        Schema::dropIfExists('hr_competency_interviews');
    }
};
