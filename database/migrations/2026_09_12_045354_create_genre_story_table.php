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
        Schema::create('genre_story_books', function (Blueprint $table) {
            $table->foreignId('genre_id')->constrained("genres")->cascadeOnDelete();
            $table->foreignId('story_book_id')->constrained("story_books")->cascadeOnDelete();

            $table->primary(['genre_id', 'story_book_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('genre_story_book');
    }
};
