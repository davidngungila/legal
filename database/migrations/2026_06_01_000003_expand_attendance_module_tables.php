<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('attendances', 'status_code')) {
                $table->string('status_code', 10)->nullable()->after('status');
            }
            if (!Schema::hasColumn('attendances', 'ordinary_hours')) {
                $table->decimal('ordinary_hours', 5, 2)->default(0)->after('total_hours');
            }
            if (!Schema::hasColumn('attendances', 'rest_day_hours')) {
                $table->decimal('rest_day_hours', 5, 2)->default(0)->after('overtime_hours');
            }
            if (!Schema::hasColumn('attendances', 'ph_hours')) {
                $table->decimal('ph_hours', 5, 2)->default(0)->after('rest_day_hours');
            }
            if (!Schema::hasColumn('attendances', 'night_hours')) {
                $table->decimal('night_hours', 5, 2)->default(0)->after('ph_hours');
            }
            if (!Schema::hasColumn('attendances', 'source')) {
                $table->string('source', 30)->default('web')->after('night_hours');
            }
            if (!Schema::hasColumn('attendances', 'manual_entry')) {
                $table->boolean('manual_entry')->default(false)->after('source');
            }
            if (!Schema::hasColumn('attendances', 'workflow_status')) {
                $table->string('workflow_status', 30)->default('approved')->after('manual_entry');
            }
            if (!Schema::hasColumn('attendances', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('workflow_status');
            }
            if (!Schema::hasColumn('attendances', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('attendances', 'late_minutes')) {
                $table->integer('late_minutes')->default(0)->after('approved_at');
            }
            if (!Schema::hasColumn('attendances', 'early_departure_minutes')) {
                $table->integer('early_departure_minutes')->default(0)->after('late_minutes');
            }
            if (!Schema::hasColumn('attendances', 'violation_flags')) {
                $table->json('violation_flags')->nullable()->after('early_departure_minutes');
            }
            if (!Schema::hasColumn('attendances', 'shift_pattern_id')) {
                $table->unsignedBigInteger('shift_pattern_id')->nullable()->after('violation_flags');
            }
        });

        if (!Schema::hasTable('shift_patterns')) {
            Schema::create('shift_patterns', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->string('shift_name');
                $table->time('start_time');
                $table->time('end_time');
                $table->integer('break_duration')->default(60);
                $table->boolean('is_night_shift')->default(false);
                $table->decimal('allowance_rate', 8, 2)->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['client_id', 'is_active']);
            });
        }

        if (!Schema::hasTable('employee_shifts')) {
            Schema::create('employee_shifts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id');
                $table->unsignedBigInteger('employee_id');
                $table->unsignedBigInteger('shift_pattern_id');
                $table->date('effective_from');
                $table->date('effective_to')->nullable();
                $table->timestamps();

                $table->index(['client_id', 'employee_id']);
                $table->index(['shift_pattern_id']);
            });
        }

        if (!Schema::hasTable('public_holidays')) {
            Schema::create('public_holidays', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->date('holiday_date');
                $table->string('holiday_name');
                $table->boolean('is_recurring')->default(false);
                $table->integer('active_year')->nullable();
                $table->timestamps();

                $table->index(['client_id', 'holiday_date']);
            });
        }

        if (!Schema::hasTable('attendance_violations')) {
            Schema::create('attendance_violations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id');
                $table->unsignedBigInteger('employee_id');
                $table->unsignedBigInteger('attendance_id')->nullable();
                $table->date('violation_date');
                $table->string('violation_type');
                $table->text('details')->nullable();
                $table->string('status')->default('open');
                $table->boolean('action_triggered')->default(false);
                $table->timestamps();

                $table->index(['client_id', 'employee_id']);
                $table->index(['client_id', 'status']);
            });
        }

        if (!Schema::hasTable('attendance_monthly_summaries')) {
            Schema::create('attendance_monthly_summaries', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id');
                $table->unsignedBigInteger('employee_id');
                $table->integer('month');
                $table->integer('year');
                $table->integer('total_days')->default(0);
                $table->integer('worked_days')->default(0);
                $table->integer('absent_days')->default(0);
                $table->integer('leave_days')->default(0);
                $table->decimal('overtime_hours', 6, 2)->default(0);
                $table->decimal('night_hours', 6, 2)->default(0);
                $table->timestamps();

                $table->unique(['client_id', 'employee_id', 'month', 'year'], 'attendance_monthly_summary_unique');
                $table->index(['client_id', 'month', 'year']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_monthly_summaries');
        Schema::dropIfExists('attendance_violations');
        Schema::dropIfExists('public_holidays');
        Schema::dropIfExists('employee_shifts');
        Schema::dropIfExists('shift_patterns');

        Schema::table('attendances', function (Blueprint $table) {
            foreach ([
                'status_code',
                'ordinary_hours',
                'rest_day_hours',
                'ph_hours',
                'night_hours',
                'source',
                'manual_entry',
                'workflow_status',
                'approved_by',
                'approved_at',
                'late_minutes',
                'early_departure_minutes',
                'violation_flags',
                'shift_pattern_id',
            ] as $column) {
                if (Schema::hasColumn('attendances', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
