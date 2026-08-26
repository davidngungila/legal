<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employment_contracts', function (Blueprint $table) {
            $table->string('employee_signature_path')->nullable()->after('signed_contract_path');
            $table->string('employer_signature_path')->nullable()->after('employee_signature_path');
        });
    }

    public function down(): void
    {
        Schema::table('employment_contracts', function (Blueprint $table) {
            $table->dropColumn(['employee_signature_path', 'employer_signature_path']);
        });
    }
};
