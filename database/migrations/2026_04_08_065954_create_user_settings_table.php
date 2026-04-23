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
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('theme', 20)->default('light');
            $table->string('language', 10)->default('en');
            $table->string('timezone', 50)->default('Africa/Dar_es_Salaam');
            $table->string('date_format', 10)->default('Y-m-d');
            $table->string('time_format', 5)->default('24');
            $table->string('currency', 10)->default('TZS');
            $table->boolean('notification_email')->default(true);
            $table->boolean('notification_push')->default(true);
            $table->boolean('notification_sms')->default(false);
            $table->boolean('two_factor_enabled')->default(false);
            $table->integer('session_timeout')->default(120);
            $table->boolean('auto_logout')->default(false);
            $table->string('privacy_profile_visibility', 20)->default('team');
            $table->string('privacy_activity_visibility', 20)->default('team');
            $table->string('data_export_format', 10)->default('csv');
            $table->string('dashboard_layout', 20)->default('default');
            $table->json('preferences')->nullable();
            $table->timestamps();
            
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};
