<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserPermissionSeeder::class);

        $this->call(UserSeeder::class);

        $this->call(GenreSeeder::class);
        $this->call(AudienceSeeder::class);
        $this->call(GenreAudienceSeeder::class);
        $this->call(LanguageSeeder::class);
        $this->call(StoryBookTypeSeeder::class);
        $this->call(IllustrationTypeSeeder::class);

        $this->call(AiBrainSeeder::class);

        $this->call(AiPromptSeeder::class);

        $this->call(StoryBookGeneratorStepSeeder::class);

        $this->call(AiBrainOutputTypeSeeder::class);

        $this->call(AiBrainOutputTypeRelationshipSeeder::class);
    }
}
