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
        if (Schema::hasColumn('story_books', 'pages')) {
            Schema::table('story_books', function (Blueprint $table) {
                $table->dropColumn('pages');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('story_books', 'pages')) {
            Schema::table('story_books', function (Blueprint $table) {
                $table->jsonb('pages')->nullable();
            });
        }
    }
};