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
        Schema::create('section_student', function (Blueprint $table) {
            
            $table->id();

            // CONNECT TO SECTIONS
            $table->foreignId('section_id')
                ->constrained('sections')
                ->cascadeOnDelete();

            // CONNECT TO STUDENTS
            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            // PREVENT DUPLICATE STUDENT ASSIGNMENTS
            $table->unique([
                'section_id',
                'student_id'
            ]);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_student');
    }
};
