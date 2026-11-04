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
        Schema::create('sections', function (Blueprint $table) {
              $table->id();

            // FOREIGN KEY: SCHOOL YEAR
            $table->foreignId('school_year_id')
                ->constrained('school_years')
                ->restrictOnDelete();

            // FOREIGN KEY: STRAND
            $table->foreignId('strand_id')
                ->constrained('strands')
                ->restrictOnDelete();

            $table->string('name', 100);

            $table->enum('grade_level', ['11', '12']);

            $table->boolean('is_active')->default(true);

            // NO DUPLICATE SECTION PER GRADE / YEAR
            $table->unique(
                ['school_year_id', 'grade_level', 'name'],
                'sections_year_grade_name_unique'
            );

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
