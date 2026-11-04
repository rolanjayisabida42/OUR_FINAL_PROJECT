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
        Schema::create('submission_files', function (Blueprint $table) {
            $table->id();
           
            $table->foreignId('section_submission_id')
                ->constrained('section_submissions')
                ->cascadeOnDelete();

            $table->foreignId('submission_requirement_id')
                ->constrained('submission_requirements')
                ->restrictOnDelete();

            $table->foreignId('uploaded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('disk', 30)->default('local');
            $table->string('path', 500);
            $table->string('original_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size_bytes');

            $table->string('review_status', 20)
                ->default('pending');

            $table->text('review_remarks')->nullable();

            $table->timestamps();

            $table->index('submission_requirement_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_files');
    }
};
