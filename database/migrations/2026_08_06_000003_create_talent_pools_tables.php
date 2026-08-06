<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('talent_pools', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('name');
            $table->string('type', 50)->default('custom');
            $table->text('description')->nullable();
            $table->string('status', 20)->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->index(['client_id', 'status']);
        });

        Schema::create('talent_pool_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('talent_pool_id');
            $table->unsignedBigInteger('employee_id');
            $table->string('readiness', 30)->default('developing');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('added_by')->nullable();
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('talent_pool_id')->references('id')->on('talent_pools')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->unique(['talent_pool_id', 'employee_id']);
            $table->index(['client_id', 'readiness']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('talent_pool_members');
        Schema::dropIfExists('talent_pools');
    }
};
