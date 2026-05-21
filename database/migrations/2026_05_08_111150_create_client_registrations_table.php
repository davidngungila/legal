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
        Schema::create('client_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('employer_name');
            $table->string('employer_number')->unique();
            $table->string('contact_person');
            $table->string('contact_phone');
            $table->string('contact_email');
            $table->string('tin_number')->unique();
            $table->string('tin_certificate_path')->nullable();
            $table->string('osha_registration')->unique();
            $table->string('osha_certificate_path')->nullable();
            $table->string('nhif_registration')->unique();
            $table->string('nhif_certificate_path')->nullable();
            $table->string('wcf_registration')->unique();
            $table->string('wcf_certificate_path')->nullable();
            $table->string('vat_registration_number')->unique();
            $table->string('vat_certificate_path')->nullable();
            $table->string('nssf_registration')->unique();
            $table->string('nssf_certificate_path')->nullable();
            $table->string('phone');
            $table->string('mobile');
            $table->string('email');
            $table->string('postal_address')->nullable();
            $table->string('region');
            $table->string('district');
            $table->string('fax')->nullable();
            $table->string('location');
            $table->string('road');
            $table->string('street');
            $table->string('plot')->nullable();
            $table->string('block')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['employer_name', 'employer_number']);
            $table->index('tin_number');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_registrations');
    }
};
