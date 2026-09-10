<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('genre_audience', function (Blueprint $table) {
            $table->foreignId('genre_id')
                ->constrained('genres')
                ->cascadeOnDelete();

            $table->foreignId('audience_id')
                ->constrained('audiences')
                ->cascadeOnDelete();

            $table->primary([
                'genre_id',
                'audience_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('genre_audience');
    }
};