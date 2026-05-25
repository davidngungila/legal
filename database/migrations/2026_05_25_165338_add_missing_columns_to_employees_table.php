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
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'currency')) {
                $table->string('currency', 3)->default('TZS')->after('salary');
            }
            if (!Schema::hasColumn('employees', 'payment_frequency')) {
                $table->string('payment_frequency')->default('monthly')->after('currency');
            }
            if (!Schema::hasColumn('employees', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('payment_frequency');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['currency', 'payment_frequency', 'created_by']);
        });
    }
};
