<?php

namespace Database\Factories;

use App\Models\StoryType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StoryType>
 */
class StoryTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::where('is_super_admin', true)->inRandomOrder()->first();

        $name = $this->faker->name();
        $brief = $this->faker->sentence();
        $promptIns = $this->faker->paragraphs(2, true);

        return [
            'name' => $name,
            'brief' => $brief,
            'prompt_instruction' => $promptIns,
            'created_by_id' => $user?->id ?? '1',
        ];
    }
}