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
        Schema::create('submission_requirements', function (Blueprint $table) {
           $table->id();

            $table->foreignId('submission_setting_id')
                ->constrained('submission_settings')
                ->cascadeOnDelete();

            $table->string('name', 120);
            $table->string('description', 500)->nullable();

            $table->unsignedSmallInteger('position')->default(0);

            $table->timestamps();

            $table->index([
                'submission_setting_id',
                'position',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_requirements');
    }
};
