<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained('clients')->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('category')->nullable();
            $table->string('provider')->nullable();
            $table->text('description')->nullable();
            $table->decimal('cost', 14, 2)->default(0);
            $table->string('currency', 3)->default('TZS');
            $table->decimal('duration_hours', 8, 2)->default(0);
            $table->boolean('is_certification')->default(false);
            $table->string('status')->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index(['client_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_programs');
    }
};
