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

            $table->longText('additional_information')->nullable();



            $table->jsonb('foundation')->nullable();

            $table->jsonb('characters')->nullable();
            $table->jsonb('world_bible')->nullable();
            $table->jsonb('locations')->nullable();
            $table->jsonb('factions')->nullable();
            $table->jsonb('creatures')->nullable();
            $table->jsonb('systems')->nullable();
            $table->jsonb('timeline')->nullable();
            $table->jsonb('story_structure')->nullable();
            $table->jsonb('twists_and_foreshadowing')->nullable();
            $table->jsonb('scene_plans')->nullable();
            $table->jsonb('dialogue_plans')->nullable();
            $table->jsonb('page_plan')->nullable();
            $table->jsonb('pages')->nullable();

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
