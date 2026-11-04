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
        Schema::create('students', function (Blueprint $table) {
            $table->id();

               // BASIC INFORMATION
            $table->string('lrn', 12)->unique();
            $table->string('last_name', 120);
            $table->string('first_name', 120);
            $table->string('middle_name', 120)->nullable();
            $table->enum('sex', ['M', 'F']);
            $table->date('birth_date');

            // ADDITIONAL INFORMATION
            $table->string('birth_place_province', 150)->nullable();
            $table->string('mother_tongue', 120)->nullable();
            $table->string('ip_ethnic_group', 150)->nullable();
            $table->string('religion', 150)->nullable();

            // ADDRESS
            $table->string('house_street_sitio_purok')->nullable();
            $table->string('barangay', 150)->nullable();
            $table->string('municipality_city', 150)->nullable();
            $table->string('province', 150)->nullable();

            // PARENT INFORMATION
            $table->string('father_first_name', 120)->nullable();
            $table->string('father_last_name', 120)->nullable();

            $table->string('mother_maiden_first_name', 120)->nullable();
            $table->string('mother_maiden_middle_name', 120)->nullable();
            $table->string('mother_maiden_last_name', 120)->nullable();

            // GUARDIAN INFORMATION
            $table->string('guardian_name', 200)->nullable();
            $table->string('guardian_relationship', 120)->nullable();
            $table->string('contact_number', 30)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
