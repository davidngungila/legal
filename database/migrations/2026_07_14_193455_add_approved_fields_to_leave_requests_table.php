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
        Schema::table('leave_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('leave_requests', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('leave_requests', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('approved_at');
            }
            if (!Schema::hasColumn('leave_requests', 'applied_at')) {
                $table->timestamp('applied_at')->nullable()->after('rejection_reason');
            }
            if (!Schema::hasColumn('leave_requests', 'applied_by')) {
                $table->foreignId('applied_by')->nullable()->constrained('users')->onDelete('set null')->after('applied_at');
            }
            if (!Schema::hasColumn('leave_requests', 'leave_type_id')) {
                $table->foreignId('leave_type_id')->nullable()->constrained('leave_types')->onDelete('set null')->after('leave_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            if (Schema::hasColumn('leave_requests', 'applied_by')) {
                $table->dropForeign(['applied_by']);
            }
            if (Schema::hasColumn('leave_requests', 'leave_type_id')) {
                $table->dropForeign(['leave_type_id']);
            }
            $table->dropColumn(['approved_at', 'rejection_reason', 'applied_at', 'applied_by', 'leave_type_id']);
        });
    }
};
