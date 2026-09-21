<?php

namespace Database\Seeders;

use App\Helpers\SeederHelper;
use App\Models\AiPrompt;
use App\Models\StoryBookGeneratorStep;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StoryBookGeneratorStepSeeder extends Seeder
{
    public function run(): void
    {
        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
            StoryBookGeneratorStep::query()->delete();
            DB::statement("DELETE FROM sqlite_sequence WHERE name='story_book_generator_steps'");
            DB::statement('PRAGMA foreign_keys = ON;');
        }

        if (config('database.default') === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            StoryBookGeneratorStep::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        if (in_array(config('database.default'), ['pgsql', 'sqlsrv'])) {
            StoryBookGeneratorStep::truncate();
        }

        $stepData = SeederHelper::storyBookGeneratorSteps();

        $stepNames = $stepData->pluck('name');

        if ($stepNames->unique()->count() !== $stepNames->count()) {
            throw new \RuntimeException(
                'SeederHelper::storyBookGeneratorSteps() contains duplicate step names.'
            );
        }

        $steps = [];

        foreach ($stepData as $generatorStep) {
            $aiPromptId = AiPrompt::where(
                'code',
                $generatorStep->ai_prompt_code
            )->value('id');

            $steps[] = StoryBookGeneratorStep::factory()
                ->state([
                    'name' => $generatorStep->name,
                    'ai_prompt_id' => $aiPromptId,
                ])
                ->create();
        }

        $stepsByCode = collect($steps)->keyBy(
            fn (StoryBookGeneratorStep $step) => Str::studly($step->name)
        );

        foreach ($steps as $index => $step) {
            $generatorStep = $stepData[$index];

            $step->previous_step_id = $steps[$index - 1]->id ?? null;
            $step->next_step_id = ($steps[$index + 1] ?? null)?->id;

            $dependencyIds = [];

            foreach ($generatorStep->depends_on ?? [] as $dependencyCode) {
                $dependencyStep = $stepsByCode->get($dependencyCode);

                if (! $dependencyStep) {
                    continue;
                }

                $dependencyIds[] = $dependencyStep->id;
            }

            $dependencyIds = array_values(array_unique($dependencyIds));

            sort($dependencyIds);

            $step->depend_on_step_ids = count($dependencyIds) > 0
                ? $dependencyIds
                : null;

            $step->save();
        }
    }
}
