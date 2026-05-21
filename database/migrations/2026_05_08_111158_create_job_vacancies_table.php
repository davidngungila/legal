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
        Schema::create('job_vacancies', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('job_title');
            $table->enum('vacancy_type', ['new_position', 'replacement']);
            $table->date('position_vacant_date');
            $table->date('application_date');
            $table->date('application_deadline');
            $table->string('department');
            $table->string('workstation');
            $table->text('replacement_reason')->nullable();
            $table->text('job_description');
            $table->integer('min_age')->nullable();
            $table->text('academic_qualifications')->nullable();
            $table->text('professional_qualifications')->nullable();
            $table->text('other_qualifications')->nullable();
            $table->decimal('salary_range_min', 10, 2)->nullable();
            $table->decimal('salary_range_max', 10, 2)->nullable();
            $table->text('additional_comments')->nullable();
            $table->enum('status', ['draft', 'submitted', 'supervisor_approved', 'manager_recommended', 'hr_approved', 'rejected', 'closed'])->default('draft');
            $table->unsignedBigInteger('initiated_by')->nullable();
            $table->unsignedBigInteger('supervisor_id')->nullable();
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->unsignedBigInteger('hr_manager_id')->nullable();
            $table->timestamp('supervisor_approved_at')->nullable();
            $table->timestamp('manager_recommended_at')->nullable();
            $table->timestamp('hr_approved_at')->nullable();
            $table->text('shortlisted_file_path')->nullable();
            $table->text('signed_file_path')->nullable();
            $table->timestamps();
            
            $table->foreign('initiated_by')->references('id')->on('users');
            $table->foreign('supervisor_id')->references('id')->on('users');
            $table->foreign('manager_id')->references('id')->on('users');
            $table->foreign('hr_manager_id')->references('id')->on('users');
            $table->index(['company_name', 'job_title']);
            $table->index('status');
            $table->index('application_deadline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_vacancies');
    }
};
