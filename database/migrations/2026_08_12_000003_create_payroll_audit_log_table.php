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
        if (!Schema::hasTable('payroll_audit_logs')) {
            Schema::create('payroll_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id');
                $table->unsignedBigInteger('payroll_id')->nullable();
                $table->unsignedBigInteger('employee_id')->nullable();
                $table->string('action'); // CREATE, UPDATE, DELETE, APPROVE, REJECT, LOCK, REVERSE
                $table->unsignedBigInteger('performed_by');
                $table->timestamp('performed_at');
                $table->text('old_value')->nullable();
                $table->text('new_value')->nullable();
                $table->text('reason')->nullable();
                $table->string('ip_address')->nullable();
                $table->string('user_agent')->nullable();
                $table->timestamps();

                $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
                $table->foreign('payroll_id')->references('id')->on('payrolls')->onDelete('set null');
                $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null');
                $table->foreign('performed_by')->references('id')->on('users')->onDelete('cascade');
                
                $table->index(['client_id', 'payroll_id']);
                $table->index(['client_id', 'employee_id']);
                $table->index(['client_id', 'performed_at']);
                $table->index(['action', 'performed_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_audit_logs');
    }
};
