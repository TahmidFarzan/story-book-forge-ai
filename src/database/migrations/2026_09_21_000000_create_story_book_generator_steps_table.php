<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('story_book_generator_steps', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->unique();
            $table->string('slug')->unique();
            $table->json('depend_on_step_ids')->nullable();
            $table->foreignId('previous_step_id')->nullable()->constrained('story_book_generator_steps')->nullOnDelete();
            $table->foreignId('next_step_id')->nullable()->constrained('story_book_generator_steps')->nullOnDelete();
            $table->foreignId('ai_prompt_id')->constrained('ai_prompts')->restrictOnDelete();
            $table->timestamps();

            $table->index('previous_step_id');
            $table->index('next_step_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('story_book_generator_steps');
    }
};
