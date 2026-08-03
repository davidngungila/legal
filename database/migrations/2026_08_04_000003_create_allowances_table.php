<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('allowances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->string('name');
            $table->string('type')->default('fixed');
            $table->decimal('amount', 12, 2)->default(0);
            $table->decimal('percentage', 8, 2)->default(0);
            $table->string('currency', 3)->default('TZS');
            $table->string('frequency', 20)->default('monthly');
            $table->boolean('is_taxable')->default(true);
            $table->boolean('is_active')->default(true);
            $table->date('effective_date')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('allowances');
    }
};
