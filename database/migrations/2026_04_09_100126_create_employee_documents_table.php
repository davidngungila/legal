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
        if (!Schema::hasTable('employee_documents')) {
            Schema::create('employee_documents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained()->onDelete('cascade');
                $table->string('document_type', 50);
                $table->string('document_name', 255);
                $table->string('file_path', 500);
                $table->string('mime_type', 100);
                $table->integer('file_size');
                $table->date('expiry_date')->nullable();
                $table->enum('status', ['active', 'expired', 'revoked']);
                $table->text('description')->nullable();
                $table->foreignId('uploaded_by')->nullable()->constrained()->onDelete('set null');
                $table->timestamps();
                
                $table->index(['employee_id', 'document_type']);
                $table->index(['employee_id', 'status']);
                $table->index(['employee_id', 'expiry_date']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_documents');
    }
};
