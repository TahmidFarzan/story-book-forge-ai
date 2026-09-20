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
        Schema::create('story_book_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('story_book_id')->constrained('story_books')->cascadeOnDelete();

            $table->unsignedInteger('no');
            $table->longText('narration')->nullable();

            $table->longText('illustration_prompt')->nullable();
            $table->longText('illustration_type_prompt_instruction')->nullable();

            $table->string('slug')->unique();
            $table->foreignId('created_by_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['no','story_book_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('story_book_pages');
    }
};
