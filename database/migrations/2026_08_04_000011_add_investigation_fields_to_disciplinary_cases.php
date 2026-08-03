<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('disciplinary_cases', function (Blueprint $table) {
            $table->string('investigator')->nullable();
            $table->date('investigation_started_at')->nullable();
            $table->text('investigation_findings')->nullable();
            $table->text('recommendation')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('disciplinary_cases', function (Blueprint $table) {
            $table->dropColumn(['investigator', 'investigation_started_at', 'investigation_findings', 'recommendation']);
        });
    }
};
