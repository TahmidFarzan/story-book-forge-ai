<?php
namespace Database\Factories;

use App\Models\AiBrainOutputType;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class AiBrainOutputTypeFactory extends Factory
{
    public function definition(): array
    {
        $user = User::where("is_super_admin", true)->inRandomOrder()->first();

        $name = $this->faker->randomElement(['Image', 'Text']);

        return [
            'name'                 => $name,
            'code'                 => Str::studly($name),
            'brief'                => $this->faker->sentence,
            'created_by_id'        => $user?->id ?? "1",
        ];
    }
}