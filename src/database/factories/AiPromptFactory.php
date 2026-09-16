<?php
namespace Database\Factories;

use App\Models\AiPrompt;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AiPrompt>
 */
class AiPromptFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::where("is_super_admin", true)->inRandomOrder()->first();

        $name =  $this->faker->unique()->words(3, true);
        return [
            'name'                 => $name,
            'code'                 => Str::studly($name),
            'prompt'               => $this->faker->paragraphs(4, true),
            'created_by_id'        => $user?->id ?? "1",
            'step_number'          => 0,
            'depend_on_prompt_ids' => null,
        ];
    }
}
