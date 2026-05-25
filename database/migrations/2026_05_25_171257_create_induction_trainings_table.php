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
        Schema::create('induction_trainings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedBigInteger('employee_registration_id')->nullable();
            $table->date('training_date');
            $table->string('training_type');
            $table->string('training_title');
            $table->text('training_description');
            $table->string('trainer_name');
            $table->decimal('training_duration_hours', 5, 2);
            $table->string('training_materials_path')->nullable();
            $table->string('completion_certificate_path')->nullable();
            $table->decimal('assessment_score', 5, 2)->nullable();
            $table->boolean('assessment_passed')->default(false);
            $table->text('feedback_comments')->nullable();
            $table->date('next_training_date')->nullable();
            $table->string('status')->default('scheduled');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('employee_registration_id')->references('id')->on('employee_registrations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('induction_trainings');
    }
};
