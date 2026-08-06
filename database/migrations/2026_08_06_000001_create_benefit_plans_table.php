<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('benefit_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('name');
            $table->string('category', 50)->default('health');
            $table->string('provider')->nullable();
            $table->text('description')->nullable();
            $table->decimal('cost', 15, 2)->default(0);
            $table->string('cost_period', 20)->default('monthly');
            $table->string('coverage', 100)->nullable();
            $table->boolean('mandatory')->default(false);
            $table->string('status', 20)->default('active');
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->index(['client_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('benefit_plans');
    }
};
