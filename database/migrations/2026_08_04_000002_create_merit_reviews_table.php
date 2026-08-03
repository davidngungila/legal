<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('merit_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('employee_id');
            $table->string('review_period', 50)->nullable();
            $table->unsignedTinyInteger('rating')->nullable();
            $table->decimal('old_salary', 12, 2)->default(0);
            $table->decimal('new_salary', 12, 2)->default(0);
            $table->decimal('increment_amount', 12, 2)->default(0);
            $table->decimal('increment_percent', 8, 2)->default(0);
            $table->text('reviewer_notes')->nullable();
            $table->string('status')->default('draft');
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->date('review_date')->nullable();
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merit_reviews');
    }
};
