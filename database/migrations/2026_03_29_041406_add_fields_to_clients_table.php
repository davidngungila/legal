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
        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasColumn('clients', 'registration_number')) {
                $table->string('registration_number')->nullable()->after('name');
            }
            if (!Schema::hasColumn('clients', 'tin_number')) {
                $table->string('tin_number')->nullable()->after('registration_number');
            }
            if (!Schema::hasColumn('clients', 'nssf_employer_number')) {
                $table->string('nssf_employer_number')->nullable()->after('tin_number');
            }
            if (!Schema::hasColumn('clients', 'wcf_employer_number')) {
                $table->string('wcf_employer_number')->nullable()->after('nssf_employer_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $columns = [
                'registration_number',
                'tin_number',
                'nssf_employer_number',
                'wcf_employer_number'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('clients', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
