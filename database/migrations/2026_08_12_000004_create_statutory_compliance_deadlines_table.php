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
        if (!Schema::hasTable('statutory_compliance_deadlines')) {
            Schema::create('statutory_compliance_deadlines', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id');
                $table->string('authority'); // NSSF, TRA, WCF, OSHA, etc.
                $table->string('filing_type'); // PAYE, SDL, NSSF, WCF, Accident Report, etc.
                $table->string('deadline_type'); // monthly, quarterly, annual, adhoc
                $table->date('filing_period_start');
                $table->date('filing_period_end');
                $table->date('due_date');
                $table->date('actual_filing_date')->nullable();
                $table->enum('status', ['pending', 'submitted', 'late', 'overdue'])->default('pending');
                $table->decimal('amount', 15, 2)->nullable();
                $table->text('reference_number')->nullable();
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('submitted_by')->nullable();
                $table->timestamp('submitted_at')->nullable();
                $table->unsignedBigInteger('acknowledged_by')->nullable();
                $table->timestamp('acknowledged_at')->nullable();
                $table->timestamps();

                $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
                $table->foreign('submitted_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('acknowledged_by')->references('id')->on('users')->onDelete('set null');
                
                $table->index(['client_id', 'authority']);
                $table->index(['client_id', 'status']);
                $table->index(['client_id', 'due_date']);
                $table->index(['authority', 'due_date']);
            });
        }

        // Note: Default statutory deadlines will be created per client via seeder or client creation logic
        // Each client has different filing periods and requirements
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statutory_compliance_deadlines');
    }
};
