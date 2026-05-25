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
        $tables = [
            'job_vacancies',
            'hr_competency_interviews',
            'technical_interviews',
            'employee_registrations'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (!Schema::hasColumn($tableName, 'client_id')) {
                        $table->unsignedBigInteger('client_id')->nullable()->after('id');
                        $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'job_vacancies',
            'hr_competency_interviews',
            'technical_interviews',
            'employee_registrations'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (Schema::hasColumn($tableName, 'client_id')) {
                        $table->dropForeign(['client_id']);
                        $table->dropColumn('client_id');
                    }
                });
            }
        }
    }
};
