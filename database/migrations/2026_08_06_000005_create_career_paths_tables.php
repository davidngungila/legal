<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('career_paths', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('name');
            $table->string('department')->nullable();
            $table->text('description')->nullable();
            $table->string('status', 20)->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->index(['client_id', 'status']);
        });

        Schema::create('career_path_levels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('career_path_id');
            $table->unsignedInteger('level_order')->default(1);
            $table->string('title');
            $table->string('typical_time')->nullable();
            $table->text('competencies')->nullable();
            $table->text('responsibilities')->nullable();
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('career_path_id')->references('id')->on('career_paths')->onDelete('cascade');
            $table->unique(['career_path_id', 'level_order']);
        });

        Schema::create('career_path_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('career_path_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedInteger('current_level_order')->default(1);
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('career_path_id')->references('id')->on('career_paths')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->unique(['career_path_id', 'employee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('career_path_members');
        Schema::dropIfExists('career_path_levels');
        Schema::dropIfExists('career_paths');
    }
};
