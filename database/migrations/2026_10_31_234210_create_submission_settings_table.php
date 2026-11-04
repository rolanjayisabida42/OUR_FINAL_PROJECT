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
        Schema::create('submission_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')
                ->unique()
                ->constrained('semesters')
                ->restrictOnDelete();

            $table->enum('grade_scope', ['11', '12', 'both'])
                ->default('both');

            $table->dateTime('opens_at')->nullable();
            $table->dateTime('deadline_at')->nullable();

            $table->enum('status', ['draft', 'active', 'closed'])
                ->default('draft');

            $table->text('instructions')->nullable();

            $table->foreignId('configured_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_settings');
    }
};
