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
        Schema::create('story_books', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('sub_title', 255);
            $table->timestamp('datetime')->nullable();
            $table->foreignId('audience_id')->constrained('audiences')->cascadeOnDelete();
            $table->foreignId('story_book_type_id')->constrained('story_book_types')->cascadeOnDelete();
            $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();

            $table->jsonb('received_inputs')->nullable();
            $table->longText('ai_prompt')->nullable();

            $table->longText('plot')->nullable();

            $table->string('status', 50)->nullable();
            $table->string('slug')->unique();
            $table->foreignId('created_by_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stories');
    }
};
