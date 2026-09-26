<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('story_books', function (Blueprint $table) {
            $this->addColumn($table, 'illustration_type_id', 'foreignId', 'illustration_types');
            $this->addColumn($table, 'ai_brain_text_id', 'foreignId', 'ai_brains');
            $this->addColumn($table, 'ai_brain_illustration_id', 'foreignId', 'ai_brains');

            $this->addColumn($table, 'current_step', 'unsignedSmallInteger');
            $this->addColumn($table, 'completed_steps_count', 'unsignedSmallInteger');
            $this->addColumn($table, 'current_illustration_page', 'unsignedSmallInteger');
            $this->addColumn($table, 'completed_illustration_pages', 'unsignedSmallInteger');

            $this->addColumn($table, 'error_message', 'text');
            $this->addColumn($table, 'stopped_at', 'timestamp');
            $this->addColumn($table, 'text_generation_started_at', 'timestamp');
            $this->addColumn($table, 'text_generation_completed_at', 'timestamp');
            $this->addColumn($table, 'illustration_generation_started_at', 'timestamp');
            $this->addColumn($table, 'illustration_generation_completed_at', 'timestamp');
        });
    }

    public function down(): void
    {
        Schema::table('story_books', function (Blueprint $table) {
            foreach ($this->columns() as $column) {
                if (Schema::hasColumn('story_books', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    private function addColumn(Blueprint $table, string $column, string $type, ?string $references = null): void
    {
        if (Schema::hasColumn('story_books', $column)) {
            return;
        }

        match (true) {
            $references !== null => $table->{$type}($column)->nullable()->constrained($references)->nullOnDelete(),
            $column === 'completed_steps_count', $column === 'completed_illustration_pages' => $table->{$type}($column)->default(0),
            default => $table->{$type}($column)->nullable(),
        };
    }

    private function columns(): array
    {
        return [
            'illustration_type_id',
            'ai_brain_text_id',
            'ai_brain_illustration_id',
            'current_step',
            'completed_steps_count',
            'current_illustration_page',
            'completed_illustration_pages',
            'error_message',
            'stopped_at',
            'text_generation_started_at',
            'text_generation_completed_at',
            'illustration_generation_started_at',
            'illustration_generation_completed_at',
        ];
    }
};
