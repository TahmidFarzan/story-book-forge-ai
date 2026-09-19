<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('story_books', function (Blueprint $table) {
            if (! Schema::hasColumn('story_books', 'pages')) {
                $table->jsonb('pages')->nullable();
            }
        });

        Schema::table('story_books', function (Blueprint $table) {
            if (Schema::hasColumn('story_books', 'complete_story_book')) {
                $table->dropColumn('complete_story_book');
            }
        });
    }

    public function down(): void
    {
        Schema::table('story_books', function (Blueprint $table) {
            if (! Schema::hasColumn('story_books', 'complete_story_book')) {
                $table->jsonb('complete_story_book')->nullable();
            }
        });

        Schema::table('story_books', function (Blueprint $table) {
            if (Schema::hasColumn('story_books', 'pages')) {
                $table->dropColumn('pages');
            }
        });
    }
};