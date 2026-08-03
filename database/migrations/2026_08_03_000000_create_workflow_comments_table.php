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
        Schema::create('workflow_comments', function (Blueprint $table) {
            $table->id();
            $table->string('workflow_type', 100);
            $table->unsignedBigInteger('workflow_id');
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->text('comment');
            $table->timestamps();

            $table->index(['workflow_type', 'workflow_id']);
            $table->foreign('client_id')->references('id')->on('clients')->nullOnDelete();
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_comments');
    }
};
