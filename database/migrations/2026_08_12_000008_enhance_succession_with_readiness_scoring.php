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
        // Add readiness scoring and 9-box grid fields to existing succession tables
        Schema::table('succession_readiness', function (Blueprint $table) {
            if (!Schema::hasColumn('succession_readiness', 'readiness_rating')) {
                $table->enum('readiness_rating', ['ready_now', '12_months', '24_months', 'developmental'])->nullable()->after('development_needs');
            }
            if (!Schema::hasColumn('succession_readiness', 'nine_box_position')) {
                $table->string('nine_box_position', 10)->nullable()->after('readiness_rating'); // 1-9 grid position
            }
            if (!Schema::hasColumn('succession_readiness', 'performance_score')) {
                $table->decimal('performance_score', 3, 2)->nullable()->after('nine_box_position'); // 1-5 scale
            }
            if (!Schema::hasColumn('succession_readiness', 'potential_score')) {
                $table->decimal('potential_score', 3, 2)->nullable()->after('performance_score'); // 1-5 scale
            }
            if (!Schema::hasColumn('succession_readiness', 'risk_of_loss')) {
                $table->enum('risk_of_loss', ['low', 'medium', 'high'])->default('medium')->after('potential_score');
            }
            if (!Schema::hasColumn('succession_readiness', 'impact_of_loss')) {
                $table->enum('impact_of_loss', ['low', 'medium', 'high'])->default('medium')->after('risk_of_loss');
            }
            if (!Schema::hasColumn('succession_readiness', 'target_date')) {
                $table->date('target_date')->nullable()->after('impact_of_loss');
            }
        });

        // Create 9-box grid reference table
        if (!Schema::hasTable('nine_box_grid_definitions')) {
            Schema::create('nine_box_grid_definitions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->integer('grid_position'); // 1-9
                $table->string('position_label'); // e.g., "Future Star", "Inconsistent", etc.
                $table->decimal('performance_min', 3, 2)->default(1);
                $table->decimal('performance_max', 3, 2)->default(5);
                $table->decimal('potential_min', 3, 2)->default(1);
                $table->decimal('potential_max', 3, 2)->default(5);
                $table->text('description')->nullable();
                $table->text('recommended_action')->nullable();
                $table->boolean('is_default')->default(true);
                $table->timestamps();

                $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
                
                $table->index(['client_id', 'grid_position']);
            });
        }

        // Insert default 9-box grid definitions
        DB::table('nine_box_grid_definitions')->insert([
            // Row 1: High Potential
            [
                'client_id' => null,
                'grid_position' => 1,
                'position_label' => 'Inconsistent',
                'performance_min' => 1,
                'performance_max' => 2.99,
                'potential_min' => 4,
                'potential_max' => 5,
                'description' => 'High potential but inconsistent performance',
                'recommended_action' => 'Focus on performance improvement',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_id' => null,
                'grid_position' => 2,
                'position_label' => 'High Potential',
                'performance_min' => 3,
                'performance_max' => 3.99,
                'potential_min' => 4,
                'potential_max' => 5,
                'description' => 'Good performance with high potential',
                'recommended_action' => 'Develop for future leadership',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_id' => null,
                'grid_position' => 3,
                'position_label' => 'Future Star',
                'performance_min' => 4,
                'performance_max' => 5,
                'potential_min' => 4,
                'potential_max' => 5,
                'description' => 'Top performer with highest potential',
                'recommended_action' => 'Fast-track to leadership',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Row 2: Medium Potential
            [
                'client_id' => null,
                'grid_position' => 4,
                'position_label' => 'Talent Risk',
                'performance_min' => 1,
                'performance_max' => 2.99,
                'potential_min' => 3,
                'potential_max' => 3.99,
                'description' => 'Low performance with medium potential',
                'recommended_action' => 'Performance improvement plan',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_id' => null,
                'grid_position' => 5,
                'position_label' => 'Core Contributor',
                'performance_min' => 3,
                'performance_max' => 3.99,
                'potential_min' => 3,
                'potential_max' => 3.99,
                'description' => 'Solid performer with medium potential',
                'recommended_action' => 'Maintain and develop',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_id' => null,
                'grid_position' => 6,
                'position_label' => 'High Professional',
                'performance_min' => 4,
                'performance_max' => 5,
                'potential_min' => 3,
                'potential_max' => 3.99,
                'description' => 'High performer with medium potential',
                'recommended_action' => 'Develop for specialist roles',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Row 3: Low Potential
            [
                'client_id' => null,
                'grid_position' => 7,
                'position_label' => 'Ineffective',
                'performance_min' => 1,
                'performance_max' => 2.99,
                'potential_min' => 1,
                'potential_max' => 2.99,
                'description' => 'Low performance with low potential',
                'recommended_action' => 'Performance management required',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_id' => null,
                'grid_position' => 8,
                'position_label' => 'Steady Performer',
                'performance_min' => 3,
                'performance_max' => 3.99,
                'potential_min' => 1,
                'potential_max' => 2.99,
                'description' => 'Good performer with limited potential',
                'recommended_action' => 'Maintain in current role',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_id' => null,
                'grid_position' => 9,
                'position_label' => 'Trusted Professional',
                'performance_min' => 4,
                'performance_max' => 5,
                'potential_min' => 1,
                'potential_max' => 2.99,
                'description' => 'High performer with limited advancement potential',
                'recommended_action' => 'Retain and reward in current role',
                'is_default' => true,
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
        Schema::dropIfExists('nine_box_grid_definitions');
        
        Schema::table('succession_readiness', function (Blueprint $table) {
            $columns = [
                'readiness_rating', 'nine_box_position', 'performance_score',
                'potential_score', 'risk_of_loss', 'impact_of_loss',
                'target_date'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('succession_readiness', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
