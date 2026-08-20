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
        if (!Schema::hasTable('paye_tax_bands')) {
            Schema::create('paye_tax_bands', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->decimal('lower_limit', 15, 2)->default(0);
                $table->decimal('upper_limit', 15, 2)->nullable();
                $table->decimal('rate', 5, 2); // Percentage rate
                $table->decimal('cumulative_tax', 15, 2)->default(0); // Tax on lower limit
                $table->date('effective_date')->default(now());
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['client_id', 'is_active', 'effective_date']);
            });
        }

        // Insert default TRA PAYE bands for Tanzania
        DB::table('paye_tax_bands')->insert([
            [
                'lower_limit' => 0,
                'upper_limit' => 270000,
                'rate' => 0,
                'cumulative_tax' => 0,
                'effective_date' => now(),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'lower_limit' => 270001,
                'upper_limit' => 520000,
                'rate' => 8,
                'cumulative_tax' => 0,
                'effective_date' => now(),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'lower_limit' => 520001,
                'upper_limit' => 760000,
                'rate' => 20,
                'cumulative_tax' => 20000,
                'effective_date' => now(),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'lower_limit' => 760001,
                'upper_limit' => 1000000,
                'rate' => 25,
                'cumulative_tax' => 68000,
                'effective_date' => now(),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'lower_limit' => 1000001,
                'upper_limit' => null,
                'rate' => 30,
                'cumulative_tax' => 128000,
                'effective_date' => now(),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paye_tax_bands');
    }
};
