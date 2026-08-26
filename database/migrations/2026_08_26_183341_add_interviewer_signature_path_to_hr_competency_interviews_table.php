<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hr_competency_interviews', function (Blueprint $table) {
            $table->string('interviewer_signature_path')->nullable()->after('interviewer_signature');
        });
    }

    public function down(): void
    {
        Schema::table('hr_competency_interviews', function (Blueprint $table) {
            $table->dropColumn('interviewer_signature_path');
        });
    }
};
