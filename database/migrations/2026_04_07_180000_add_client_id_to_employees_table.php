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
        // Check if client_id column exists in employees table
        if (!Schema::hasColumn('employees', 'client_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->foreignId('client_id')->constrained()->onDelete('cascade')->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't want to drop the column as it might be needed
    }
};
