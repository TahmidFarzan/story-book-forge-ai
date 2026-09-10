<?php

namespace Database\Factories;

use App\Models\Language;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Language>
 */
class LanguageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::where('is_super_admin', true)->inRandomOrder()->first();

        $name = $this->faker->unique()->languageCode();
        $brief = $this->faker->sentence();

        return [
            'name' => $name,
            'brief' => $brief,
            'created_by_id' => $user?->id ?? '1',
        ];
    }
}