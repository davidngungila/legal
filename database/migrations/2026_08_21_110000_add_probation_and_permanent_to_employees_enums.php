<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE employees MODIFY status ENUM('active','inactive','terminated','on_leave','probation') NOT NULL DEFAULT 'active'");
        DB::statement("ALTER TABLE employees MODIFY employment_type ENUM('full_time','part_time','contract','intern','permanent') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE employees MODIFY status ENUM('active','inactive','terminated','on_leave') NOT NULL DEFAULT 'active'");
        DB::statement("ALTER TABLE employees MODIFY employment_type ENUM('full_time','part_time','contract','intern') NULL");
    }
};
