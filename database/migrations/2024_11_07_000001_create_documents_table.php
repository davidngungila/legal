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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('document_type'); // contract, handbook, policy, safety, procedure, form, report, other
            $table->string('file_path')->nullable();
            $table->integer('file_size')->nullable();
            $table->string('file_type')->nullable(); // pdf, doc, docx, xls, xlsx, etc.
            $table->string('status')->default('active'); // active, draft, archived, expired
            $table->string('version')->default('1.0');
            $table->date('effective_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_required')->default(false);
            $table->boolean('is_public')->default(true); // Public means visible to all employees
            $table->string('category')->nullable(); // HR, Safety, Finance, Operations, etc.
            $table->json('tags')->nullable();
            $table->timestamps();
            
            $table->index(['client_id', 'document_type']);
            $table->index(['client_id', 'status']);
            $table->index(['client_id', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
