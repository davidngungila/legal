<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = $this->clientIdTables();

        foreach ($tables as $table) {
            $indexName = 'idx_' . $table . '_client_id';

            try {
                Schema::table($table, function (Blueprint $t) use ($indexName) {
                    $t->index('client_id', $indexName);
                });
            } catch (\Throwable $e) {
                // index already exists
            }

            try {
                Schema::table($table, function (Blueprint $t) {
                    $t->foreign('client_id')
                        ->references('id')
                        ->on('clients')
                        ->onDelete('restrict');
                });
            } catch (\Throwable $e) {
                // foreign key already exists
            }
        }
    }

    public function down(): void
    {
        $tables = $this->clientIdTables();

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropForeign(['client_id']);
                $t->dropIndex('idx_' . $t->getTable() . '_client_id');
            });
        }
    }

    private function clientIdTables(): array
    {
        $db = DB::connection()->getDatabaseName();

        return collect(DB::select(
            "SELECT DISTINCT TABLE_NAME
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = ? AND COLUMN_NAME = 'client_id' AND TABLE_NAME <> 'clients'",
            [$db]
        ))->pluck('TABLE_NAME')->toArray();
    }
};