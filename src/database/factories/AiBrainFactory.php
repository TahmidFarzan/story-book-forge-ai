<?php
namespace Database\Factories;

use App\Models\AiBrain;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AiBrain>
 */
class AiBrainFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::where("is_super_admin", true)->inRandomOrder()->first();

        $name  = $this->faker->name();
        $brief = $this->faker->sentence();

        return [
            'name'              => $name,
            'model'              => $name,
            'api_url'           => $this->faker->url(),
            'api_key'           => 'sk-' . $this->faker->sha256(),
            'brief'             => $brief,
            'focus'             => $this->faker->sentence(),
            'context_window'    => $this->faker->randomElement([8192, 16384, 32768, 65536, 131072, 262000]),
            'average_latency'   => $this->faker->randomFloat(2, 0.1, 3.0),
            'minimum_wait_time' => $this->faker->numberBetween(1, 10),
            'timeout_seconds'   => $this->faker->numberBetween(30, 120),
            'max_output_tokens' => $this->faker->randomElement([1024, 2048, 4096, 8192, null]),
            'created_by_id'     => $user?->id ?? "1",
        ];
    }
}
