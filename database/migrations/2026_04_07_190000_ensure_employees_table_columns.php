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
        // Check if employees table exists and has the required columns
        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                // Add client_id if it doesn't exist
                if (!Schema::hasColumn('employees', 'client_id')) {
                    $table->foreignId('client_id')->constrained()->onDelete('cascade')->after('id');
                }
                
                // Add other required columns if they don't exist
                if (!Schema::hasColumn('employees', 'employee_id')) {
                    $table->string('employee_id')->unique()->after('client_id');
                }
                
                if (!Schema::hasColumn('employees', 'first_name')) {
                    $table->string('first_name')->after('employee_id');
                }
                
                if (!Schema::hasColumn('employees', 'last_name')) {
                    $table->string('last_name')->after('first_name');
                }
                
                if (!Schema::hasColumn('employees', 'email')) {
                    $table->string('email')->unique()->after('last_name');
                }
                
                if (!Schema::hasColumn('employees', 'position')) {
                    $table->string('position')->after('email');
                }
                
                if (!Schema::hasColumn('employees', 'department')) {
                    $table->string('department')->after('position');
                }
                
                if (!Schema::hasColumn('employees', 'hire_date')) {
                    $table->date('hire_date')->after('department');
                }
                
                if (!Schema::hasColumn('employees', 'termination_date')) {
                    $table->date('termination_date')->nullable()->after('hire_date');
                }
                
                if (!Schema::hasColumn('employees', 'employment_type')) {
                    $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'intern'])->default('full_time')->after('termination_date');
                }
                
                if (!Schema::hasColumn('employees', 'status')) {
                    $table->enum('status', ['active', 'inactive', 'terminated', 'on_leave'])->default('active')->after('employment_type');
                }
                
                if (!Schema::hasColumn('employees', 'salary')) {
                    $table->decimal('salary', 10, 2)->nullable()->after('status');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't want to drop columns as they might be needed
    }
};
