<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exit_cases', function (Blueprint $table) {
            $table->enum('exit_type', [
                'resignation',
                'misconduct_termination',
                'retrenchment',
                'mutual_separation',
                'retirement',
                'death_in_service',
                'capacity',
            ])->change();
        });
    }

    public function down(): void
    {
        Schema::table('exit_cases', function (Blueprint $table) {
            $table->enum('exit_type', [
                'resignation',
                'misconduct_termination',
                'retrenchment',
                'mutual_separation',
                'retirement',
                'death_in_service',
            ])->change();
        });
    }
};
