<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained('clients')->cascadeOnDelete();
            $table->foreignId('program_id')->nullable()->constrained('training_programs')->cascadeOnDelete();
            $table->foreignId('plan_id')->nullable()->constrained('training_plans')->nullOnDelete();
            $table->string('title');
            $table->string('instructor')->nullable();
            $table->string('venue')->nullable();
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->integer('capacity')->default(0);
            $table->string('status')->default('scheduled');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index(['client_id', 'status']);
            $table->index(['program_id', 'start_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_sessions');
    }
};
