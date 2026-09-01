<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_registrations', function (Blueprint $table) {
            $table->string('surname')->nullable()->change();
            $table->string('first_name')->nullable()->change();
            $table->string('birthplace')->nullable()->change();
            $table->date('date_of_birth')->nullable()->change();
            $table->integer('age')->nullable()->change();
            $table->string('gender')->nullable()->change();
            $table->string('residence_area')->nullable()->change();
            $table->string('permanent_residence')->nullable()->change();
            $table->string('email_address')->nullable()->change();
            $table->string('phone_number')->nullable()->change();
            $table->string('place_of_recruitment')->nullable()->change();
            $table->string('work_station')->nullable()->change();
            $table->string('type_of_contract')->nullable()->change();
            $table->text('job_descriptions')->nullable()->change();
            $table->date('date_employed')->nullable()->change();
            $table->text('terms_conditions')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('employee_registrations', function (Blueprint $table) {
            $table->string('surname')->nullable(false)->change();
            $table->string('first_name')->nullable(false)->change();
            $table->string('birthplace')->nullable(false)->change();
            $table->date('date_of_birth')->nullable(false)->change();
            $table->integer('age')->nullable(false)->change();
            $table->string('gender')->nullable(false)->change();
            $table->string('residence_area')->nullable(false)->change();
            $table->string('permanent_residence')->nullable(false)->change();
            $table->string('email_address')->nullable(false)->change();
            $table->string('phone_number')->nullable(false)->change();
            $table->string('place_of_recruitment')->nullable(false)->change();
            $table->string('work_station')->nullable(false)->change();
            $table->string('type_of_contract')->nullable(false)->change();
            $table->text('job_descriptions')->nullable(false)->change();
            $table->date('date_employed')->nullable(false)->change();
            $table->text('terms_conditions')->nullable(false)->change();
        });
    }
};
