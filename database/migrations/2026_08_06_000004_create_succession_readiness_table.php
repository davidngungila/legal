<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('succession_readiness', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('pool_id')->nullable();
            $table->string('current_role')->nullable();
            $table->string('target_role')->nullable();
            $table->string('readiness', 30)->default('development');
            $table->text('development_needs')->nullable();
            $table->date('assessment_date')->nullable();
            $table->string('status', 20)->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('pool_id')->references('id')->on('talent_pools')->onDelete('set null');
            $table->index(['client_id', 'readiness']);
            $table->index(['client_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('succession_readiness');
    }
};
