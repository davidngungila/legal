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
        if (!Schema::hasTable('contracts')) {
            Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('contract_number', 50)->unique();
            $table->enum('contract_type', ['permanent', 'fixed_term', 'probation', 'internship', 'consultant', 'part_time', 'temporary']);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('probation_end_date')->nullable();
            $table->decimal('salary', 10, 2);
            $table->string('currency', 10)->default('TZS');
            $table->enum('payment_frequency', ['monthly', 'bi-weekly', 'weekly']);
            $table->string('work_schedule', 255)->nullable();
            $table->string('work_location', 255)->nullable();
            $table->text('job_description')->nullable();
            $table->json('responsibilities')->nullable();
            $table->json('terms_and_conditions')->nullable();
            $table->string('status', 50)->default('draft');
            $table->foreignId('created_by')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('signed_at')->nullable();
            $table->timestamp('terminated_at')->nullable();
            $table->string('termination_reason', 255)->nullable();
            $table->integer('renewal_count')->default(0);
            $table->date('last_renewal_date')->nullable();
            $table->boolean('auto_renewal')->default(false);
            $table->json('documents')->nullable();
            $table->timestamps();
            
            $table->index(['client_id', 'employee_id']);
            $table->index(['client_id', 'status']);
            $table->index(['client_id', 'contract_type']);
            $table->index(['client_id', 'end_date']);
            $table->unique(['client_id', 'contract_number']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
