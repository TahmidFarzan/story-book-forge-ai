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
        Schema::table('story_books', function (Blueprint $table) {
            foreach ([
                'story_book_continuity',
                'is_18_plus',
                'enable_mature_content',
            ] as $column) {
                if (Schema::hasColumn('story_books', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('story_books', function (Blueprint $table) {
            if (! Schema::hasColumn('story_books', 'story_book_continuity')) {
                $table->string('story_book_continuity', 255);
            }

            if (! Schema::hasColumn('story_books', 'is_18_plus')) {
                $table->boolean('is_18_plus')->default(false);
            }

            if (! Schema::hasColumn('story_books', 'enable_mature_content')) {
                $table->boolean('enable_mature_content')->default(false);
            }
        });
    }
};