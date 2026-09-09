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
        Schema::create('ai_brains', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('model', 500);
            $table->string('api_url', 500);
            $table->string('api_key', 500)->nullable();
            $table->text('brief')->nullable();
            $table->text('focus')->nullable();
            $table->string('slug')->unique();

            $table->bigInteger('context_window')->nullable();
            $table->decimal('average_latency', 8, 2);
            $table->integer('minimum_wait_time');
            $table->integer('timeout_seconds');
            $table->integer('max_output_tokens')->nullable();

            $table->foreignId('created_by_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_brains');
    }
};
