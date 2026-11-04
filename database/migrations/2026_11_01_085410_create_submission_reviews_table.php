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
        Schema::create('submission_reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('section_submission_id')
                ->constrained('section_submissions')
                ->cascadeOnDelete();

            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('decision', 20);

            $table->text('remarks')->nullable();

            // Preserves feedback even if corrected files
            // are later removed by the teacher.
            $table->json('file_feedback')->nullable();

            $table->timestamps();

            $table->index([
                'section_submission_id',
                'created_at'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_reviews');
    }
};
