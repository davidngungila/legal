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
            if (!Schema::hasColumn('employees', 'heslb_applicable')) {
                $table->boolean('heslb_applicable')->default(false)->after('nhif_number');
            }
            if (!Schema::hasColumn('employees', 'heslb_number')) {
                $table->string('heslb_number', 50)->nullable()->after('heslb_applicable');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            foreach (['heslb_number', 'heslb_applicable'] as $column) {
                if (Schema::hasColumn('employees', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
