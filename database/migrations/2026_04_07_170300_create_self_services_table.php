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
        Schema::create('self_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->enum('request_type', ['leave', 'payslip', 'contract', 'complaint', 'document_update', 'expense_claim']);
            $table->string('title');
            $table->text('description');
            $table->enum('status', ['pending', 'approved', 'rejected', 'processed'])->default('pending');
            $table->date('request_date');
            $table->date('start_date')->nullable(); // For leave requests
            $table->date('end_date')->nullable(); // For leave requests
            $table->integer('days_requested')->nullable(); // For leave requests
            $table->decimal('amount', 10, 2)->nullable(); // For expense claims
            $table->string('attachment_path')->nullable(); // For document uploads
            $table->foreignId('approved_by')->nullable()->references('id')->on('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->text('employee_notes')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['client_id', 'employee_id']);
            $table->index(['client_id', 'request_type']);
            $table->index(['client_id', 'status']);
            $table->index(['request_date']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('self_services');
    }
};
