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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('industry');
            $table->text('address');
            $table->string('city');
            $table->string('country');
            $table->string('postal_code')->nullable();
            $table->string('website')->nullable();
            $table->string('contact_person');
            $table->string('contact_title');
            $table->string('contact_email');
            $table->string('contact_phone');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->enum('subscription_plan', ['basic', 'premium', 'enterprise'])->default('basic');
            $table->integer('employee_count')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['status']);
            $table->index(['industry']);
            $table->index(['subscription_plan']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
