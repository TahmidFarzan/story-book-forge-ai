<?php

namespace Database\Factories;

use App\Models\AiPrompt;
use Illuminate\Database\Eloquent\Factories\Factory;

class StoryBookGeneratorStepFactory extends Factory
{
    public function definition(): array
    {
        $aiPrompt = AiPrompt::inRandomOrder()->first();

        return [
            'name' => $this->faker->unique()->words(3, true),
            'depend_on_step_ids' => null,
            'previous_step_id' => null,
            'next_step_id' => null,
            'ai_prompt_id' => $aiPrompt?->id ?? '1',
        ];
    }
}
