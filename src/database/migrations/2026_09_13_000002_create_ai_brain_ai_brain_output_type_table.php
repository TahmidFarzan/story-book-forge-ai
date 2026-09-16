<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_brain_ai_brain_output_type', function (Blueprint $table) {
            $table->foreignId('ai_brain_id')
                ->constrained('ai_brains')
                ->cascadeOnDelete();

            $table->foreignId('ai_brain_output_type_id')
                ->constrained('ai_brain_output_types')
                ->cascadeOnDelete();

            $table->primary([
                'ai_brain_id',
                'ai_brain_output_type_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_brain_ai_brain_output_type');
    }
};