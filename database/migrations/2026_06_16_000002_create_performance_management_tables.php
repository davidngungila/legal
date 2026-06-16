<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('performance_cycles')) {
            Schema::create('performance_cycles', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->string('cycle_type'); // monthly/quarterly/annual/mid-year
                $table->string('cycle_name');
                $table->date('period_start');
                $table->date('period_end');
                $table->string('employee_category')->nullable();
                $table->string('status')->default('draft'); // draft/active/completed
                $table->timestamps();
                $table->index(['client_id', 'period_start', 'period_end']);
            });
        }

        if (!Schema::hasTable('employee_goals')) {
            Schema::create('employee_goals', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id');
                $table->unsignedBigInteger('employee_id');
                $table->unsignedBigInteger('cycle_id')->nullable();
                $table->string('goal_title');
                $table->text('description')->nullable();
                $table->integer('kpi_count')->default(0);
                $table->decimal('weight_total')->default(100);
                $table->string('status')->default('draft');
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->timestamps();
                $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
                $table->foreign('cycle_id')->references('id')->on('performance_cycles')->onDelete('cascade');
                $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            });
        }

        if (!Schema::hasTable('kpis')) {
            Schema::create('kpis', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('goal_id');
                $table->text('kpi_description');
                $table->string('target')->nullable();
                $table->decimal('weight')->default(0);
                $table->string('measurement_unit')->nullable();
                $table->date('deadline')->nullable();
                $table->timestamps();
                $table->foreign('goal_id')->references('id')->on('employee_goals')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('appraisal_ratings')) {
            Schema::create('appraisal_ratings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('appraisal_id');
                $table->unsignedBigInteger('kpi_id')->nullable();
                $table->decimal('self_score')->nullable();
                $table->decimal('supervisor_score')->nullable();
                $table->decimal('calibrated_score')->nullable();
                $table->text('comments')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('performance_improvement_plans')) {
            Schema::create('performance_improvement_plans', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id');
                $table->unsignedBigInteger('employee_id');
                $table->unsignedBigInteger('trigger_appraisal_id')->nullable();
                $table->text('pip_objectives')->nullable();
                $table->date('start_date');
                $table->date('end_date');
                $table->string('review_frequency')->default('biweekly');
                $table->string('status')->default('active');
                $table->string('outcome')->nullable();
                $table->timestamps();
                $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('pip_reviews')) {
            Schema::create('pip_reviews', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('pip_id');
                $table->date('review_date');
                $table->unsignedBigInteger('reviewer_id')->nullable();
                $table->decimal('progress_rating')->nullable();
                $table->text('comments')->nullable();
                $table->text('action_items')->nullable();
                $table->timestamps();
                $table->foreign('pip_id')->references('id')->on('performance_improvement_plans')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('calibration_sessions')) {
            Schema::create('calibration_sessions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id');
                $table->unsignedBigInteger('cycle_id')->nullable();
                $table->unsignedBigInteger('facilitated_by')->nullable();
                $table->date('session_date')->nullable();
                $table->text('notes')->nullable();
                $table->string('status')->default('planned');
                $table->timestamps();
                $table->foreign('cycle_id')->references('id')->on('performance_cycles')->onDelete('cascade');
                $table->foreign('facilitated_by')->references('id')->on('users')->onDelete('set null');
            });
        }

        if (Schema::hasTable('performance_reviews') && !Schema::hasColumns('performance_reviews', ['client_id'])) {
            Schema::table('performance_reviews', function (Blueprint $table) {
                $table->unsignedBigInteger('client_id')->nullable()->after('id');
            });
        }

        if (Schema::hasTable('performance_reviews') && !Schema::hasColumns('performance_reviews', ['cycle_id'])) {
            Schema::table('performance_reviews', function (Blueprint $table) {
                $table->unsignedBigInteger('cycle_id')->nullable()->after('client_id');
            });
        }

        if (Schema::hasTable('performance_reviews') && !Schema::hasColumns('performance_reviews', ['self_rating'])) {
            Schema::table('performance_reviews', function (Blueprint $table) {
                $table->decimal('self_rating')->nullable()->after('reviewer_id');
                $table->decimal('supervisor_rating')->nullable()->after('self_rating');
                $table->decimal('calibrated_rating')->nullable()->after('supervisor_rating');
                $table->decimal('final_rating')->nullable()->after('calibrated_rating');
            });
        }

        if (Schema::hasTable('performance_reviews') && !Schema::hasColumns('performance_reviews', ['completed_at'])) {
            Schema::table('performance_reviews', function (Blueprint $table) {
                $table->timestamp('completed_at')->nullable()->after('status');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('calibration_sessions');
        Schema::dropIfExists('pip_reviews');
        Schema::dropIfExists('performance_improvement_plans');
        Schema::dropIfExists('appraisal_ratings');
        Schema::dropIfExists('kpis');
        Schema::dropIfExists('employee_goals');
        Schema::dropIfExists('performance_cycles');
    }
};
