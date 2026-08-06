<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('benefit_enrollments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('plan_id');
            $table->date('effective_date')->nullable();
            $table->decimal('employee_cost', 15, 2)->default(0);
            $table->decimal('employer_cost', 15, 2)->default(0);
            $table->string('status', 20)->default('enrolled');
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('plan_id')->references('id')->on('benefit_plans')->onDelete('cascade');
            $table->unique(['employee_id', 'plan_id']);
            $table->index(['client_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('benefit_enrollments');
    }
};
