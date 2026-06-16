<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('legal_cases', function (Blueprint $table) {
            if (!Schema::hasColumn('legal_cases', 'client_id')) {
                $table->unsignedBigInteger('client_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('legal_cases', 'employee_id')) {
                $table->unsignedBigInteger('employee_id')->nullable();
            }
            if (!Schema::hasColumn('legal_cases', 'case_number')) {
                $table->string('case_number')->nullable();
            }
            if (!Schema::hasColumn('legal_cases', 'case_type')) {
                $table->string('case_type')->nullable();
            }
            if (!Schema::hasColumn('legal_cases', 'subject')) {
                $table->string('subject')->nullable();
            }
            if (!Schema::hasColumn('legal_cases', 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn('legal_cases', 'opened_date')) {
                $table->date('opened_date')->nullable();
            }
            if (!Schema::hasColumn('legal_cases', 'due_date')) {
                $table->date('due_date')->nullable();
            }
            if (!Schema::hasColumn('legal_cases', 'priority')) {
                $table->string('priority')->nullable();
            }
            if (!Schema::hasColumn('legal_cases', 'status')) {
                $table->string('status')->nullable();
            }
            if (!Schema::hasColumn('legal_cases', 'assigned_to')) {
                $table->unsignedBigInteger('assigned_to')->nullable();
            }
            if (!Schema::hasColumn('legal_cases', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable();
            }
            if (!Schema::hasColumn('legal_cases', 'resolution_notes')) {
                $table->text('resolution_notes')->nullable();
            }
        });

        Schema::table('case_activities', function (Blueprint $table) {
            if (!Schema::hasColumn('case_activities', 'legal_case_id')) {
                $table->unsignedBigInteger('legal_case_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('case_activities', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable();
            }
            if (!Schema::hasColumn('case_activities', 'action')) {
                $table->string('action')->nullable();
            }
            if (!Schema::hasColumn('case_activities', 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn('case_activities', 'old_values')) {
                $table->json('old_values')->nullable();
            }
            if (!Schema::hasColumn('case_activities', 'new_values')) {
                $table->json('new_values')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('case_activities', function (Blueprint $table) {
            foreach (['legal_case_id', 'user_id', 'action', 'description', 'old_values', 'new_values'] as $column) {
                if (Schema::hasColumn('case_activities', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('legal_cases', function (Blueprint $table) {
            foreach ([
                'client_id',
                'employee_id',
                'case_number',
                'case_type',
                'subject',
                'description',
                'opened_date',
                'due_date',
                'priority',
                'status',
                'assigned_to',
                'created_by',
                'resolution_notes',
            ] as $column) {
                if (Schema::hasColumn('legal_cases', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
